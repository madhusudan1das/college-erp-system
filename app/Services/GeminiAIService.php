<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAIService
{
    protected $apiKey;
    protected $model = 'gemini-2.5-flash';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * Check if Gemini API is available
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Core method: Call Gemini API with file data and a prompt
     */
    public function callGemini(string $base64Data, string $mimeType, string $prompt): ?array
    {
        if (!$this->isAvailable()) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->timeout(120)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'inlineData' => [
                                        'mimeType' => $mimeType,
                                        'data' => $base64Data
                                    ]
                                ],
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]
            );

            if ($response->successful()) {
                $jsonRes = $response->json();
                $text = $jsonRes['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $decoded = json_decode(trim($text), true);
                return $decoded;
            }

            Log::error("Gemini API Error: HTTP " . $response->status() . " - " . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error("Gemini API Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Analyze an answer sheet file to extract metadata
     */
    public function analyzeAnswerSheet(string $filePath, string $mimeType): array
    {
        $base64 = base64_encode(file_get_contents($filePath));

        $prompt = 'You are an AI assistant for an academic examination management system. 
Analyze this uploaded answer sheet document (it may be a scanned PDF or photograph of a physical answer sheet). 
Extract all possible metadata from it. Look for:

1. Subject name (the academic subject this answer sheet belongs to)
2. Subject code (e.g., CS101, MA201)
3. Department name (e.g., Computer Science, Mathematics)
4. Exam type (internal, external, midterm, semester, supplementary, practical)
5. Semester number (1-10)
6. Student information (name, enrollment/roll number if visible)
7. Exam date (if visible)
8. Any course name visible
9. Your confidence score (0-100) in the accuracy of extracted data

Return the result strictly as a valid JSON object with these exact keys:
{
    "subject_name": "extracted subject or null",
    "subject_code": "extracted code or null",
    "department": "extracted department or null",
    "exam_type": "internal/external/midterm/semester/supplementary/practical or null",
    "semester": null or integer,
    "student_name": "name or null",
    "student_enrollment": "enrollment number or null",
    "exam_date": "YYYY-MM-DD or null",
    "course_name": "course name or null",
    "confidence_score": 75,
    "notes": "any additional observations"
}';

        $result = $this->callGemini($base64, $mimeType, $prompt);

        if ($result) {
            return [
                'success' => true,
                'data' => $result,
                'source' => 'gemini'
            ];
        }

        // Mock fallback
        return $this->mockAnswerSheetAnalysis();
    }

    /**
     * Analyze a timetable file to extract schedule data
     */
    public function analyzeTimetable(string $filePath, string $mimeType): array
    {
        $base64 = base64_encode(file_get_contents($filePath));

        $prompt = 'You are an AI assistant for an academic timetable management system.
Analyze this uploaded timetable document (it may be a scanned PDF, photograph, or digital image of a weekly class schedule).
Extract ALL schedule slots you can identify. For each slot, extract:

1. Faculty/Teacher name
2. Subject name  
3. Subject code (if visible)
4. Day of week (Monday, Tuesday, Wednesday, Thursday, Friday, Saturday)
5. Start time (in HH:MM 24-hour format)
6. End time (in HH:MM 24-hour format)
7. Room/Classroom number (if visible)
8. Course/Program name (e.g., B.Tech CS, BCA)
9. Section (e.g., A, B, C, if visible)
10. Semester (integer, if visible)
11. Department name (if visible)

Return the result strictly as a valid JSON object:
{
    "slots": [
        {
            "faculty_name": "Dr. John Smith",
            "subject_name": "Data Structures",
            "subject_code": "CS201",
            "day_of_week": "Monday",
            "start_time": "09:00",
            "end_time": "10:00",
            "room": "Room 301",
            "course_name": "B.Tech CS",
            "section": "A",
            "semester": 3,
            "department": "Computer Science"
        }
    ],
    "confidence_score": 80,
    "total_slots_found": 5,
    "notes": "any observations about data quality or ambiguous entries"
}';

        $result = $this->callGemini($base64, $mimeType, $prompt);

        if ($result && isset($result['slots'])) {
            return [
                'success' => true,
                'data' => $result,
                'source' => 'gemini'
            ];
        }

        // Mock fallback
        return $this->mockTimetableAnalysis();
    }

    /**
     * Mock fallback: answer sheet analysis
     */
    protected function mockAnswerSheetAnalysis(): array
    {
        return [
            'success' => true,
            'data' => [
                'subject_name' => 'Data Structures',
                'subject_code' => 'CS201',
                'department' => 'Computer Science',
                'exam_type' => 'internal',
                'semester' => 3,
                'student_name' => 'Sample Student',
                'student_enrollment' => 'CS2024001',
                'exam_date' => date('Y-m-d'),
                'course_name' => 'B.Tech Computer Science',
                'confidence_score' => 65,
                'notes' => 'AI features require a GEMINI_API_KEY in .env. Showing simulated extraction.'
            ],
            'source' => 'mock',
            'warning' => 'Real AI features require a GEMINI_API_KEY in the .env file. Showing simulated data.'
        ];
    }

    /**
     * Mock fallback: timetable analysis
     */
    protected function mockTimetableAnalysis(): array
    {
        return [
            'success' => true,
            'data' => [
                'slots' => [
                    [
                        'faculty_name' => 'Dr. Sample Faculty',
                        'subject_name' => 'Data Structures',
                        'subject_code' => 'CS201',
                        'day_of_week' => 'Monday',
                        'start_time' => '09:00',
                        'end_time' => '10:00',
                        'room' => 'Room 101',
                        'course_name' => 'B.Tech CS',
                        'section' => 'A',
                        'semester' => 3,
                        'department' => 'Computer Science'
                    ],
                    [
                        'faculty_name' => 'Dr. Sample Faculty',
                        'subject_name' => 'Database Systems',
                        'subject_code' => 'CS301',
                        'day_of_week' => 'Tuesday',
                        'start_time' => '11:00',
                        'end_time' => '12:00',
                        'room' => 'Room 205',
                        'course_name' => 'B.Tech CS',
                        'section' => 'A',
                        'semester' => 5,
                        'department' => 'Computer Science'
                    ],
                    [
                        'faculty_name' => 'Prof. Another Faculty',
                        'subject_name' => 'Mathematics II',
                        'subject_code' => 'MA102',
                        'day_of_week' => 'Wednesday',
                        'start_time' => '14:00',
                        'end_time' => '15:00',
                        'room' => 'Room 303',
                        'course_name' => 'B.Tech CS',
                        'section' => 'B',
                        'semester' => 2,
                        'department' => 'Mathematics'
                    ]
                ],
                'confidence_score' => 60,
                'total_slots_found' => 3,
                'notes' => 'AI features require a GEMINI_API_KEY in .env. Showing simulated extraction.'
            ],
            'source' => 'mock',
            'warning' => 'Real AI features require a GEMINI_API_KEY in the .env file. Showing simulated data.'
        ];
    }
}
