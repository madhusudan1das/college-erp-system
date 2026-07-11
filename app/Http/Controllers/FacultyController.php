<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Mark;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamAttempt;
use App\Models\Notice;
use App\Models\FacultyAttendance;
use App\Models\AnswerSheetUpload;
use App\Models\EvaluatorAssignment;
use App\Models\AppNotification;
use App\Models\AiAuditLog;
use App\Models\LeaveApplication;
use App\Models\Timetable;
use App\Services\GeminiAIService;

class FacultyController extends Controller
{
    private function getFaculty()
    {
        return Faculty::where('user_id', Auth::id())->firstOrFail();
    }

    // Dashboard
    public function dashboard()
    {
        $faculty = $this->getFaculty();
        $assigned_subjects = Subject::where('faculty_id', $faculty->id)->count();
        $assignments_count = Assignment::where('faculty_id', $faculty->id)->count();
        $exams_count = Exam::where('faculty_id', $faculty->id)->count();

        // Sample notice count
        $notices_count = Notice::whereNull('role_id')
            ->orWhere('role_id', Auth::user()->role_id)
            ->count();

        $today = date('l');
        $today_classes = Timetable::with(['subject', 'course'])
            ->where('faculty_id', $faculty->id)
            ->where('day_of_week', $today)
            ->orderBy('start_time')
            ->get();

        return view('faculty.dashboard', compact('faculty', 'assigned_subjects', 'assignments_count', 'exams_count', 'notices_count', 'today_classes'));
    }

    // Faculty Timetable
    public function timetable()
    {
        $faculty = $this->getFaculty();
        $timetable = Timetable::with(['subject', 'course', 'department'])
            ->where('faculty_id', $faculty->id)
            ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), start_time")
            ->get();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        // Detect overlapping classes (same day, overlapping time)
        $conflicts = [];
        $conflictIds = collect();
        $grouped = $timetable->groupBy('day_of_week');
        foreach ($grouped as $day => $daySlots) {
            $slotArr = $daySlots->values()->all();
            for ($i = 0; $i < count($slotArr); $i++) {
                for ($j = $i + 1; $j < count($slotArr); $j++) {
                    $a = $slotArr[$i];
                    $b = $slotArr[$j];
                    if ($a->start_time < $b->end_time && $a->end_time > $b->start_time) {
                        $conflicts[] = [
                            'day' => $day,
                            'slot_a' => $a,
                            'slot_b' => $b,
                        ];
                        $conflictIds->push($a->id);
                        $conflictIds->push($b->id);
                    }
                }
            }
        }
        $conflictIds = $conflictIds->unique()->values();

        return view('faculty.timetable', compact('timetable', 'days', 'conflicts', 'conflictIds'));
    }

    // My Attendance
    public function myAttendance()
    {
        $faculty = $this->getFaculty();
        $attendance = FacultyAttendance::where('faculty_id', $faculty->id)
            ->orderBy('date', 'desc')
            ->get();
        return view('faculty.my_attendance', compact('attendance'));
    }

    // Student Attendance
    public function attendance(Request $request)
    {
        $faculty = $this->getFaculty();
        $subjects = Subject::where('faculty_id', $faculty->id)->get();

        $selected_subject = $request->input('subject_id');
        $selected_date = $request->input('date', date('Y-m-d'));

        $students = [];
        $attendanceMap = collect();

        if ($selected_subject) {
            $subject = Subject::findOrFail($selected_subject);
            $students = Student::where('course_id', $subject->course_id)
                ->where('current_semester', $subject->semester)
                ->get();

            $attendanceMap = Attendance::where('subject_id', $selected_subject)
                ->where('date', $selected_date)
                ->pluck('status', 'student_id');
        }

        return view('faculty.attendance', compact('subjects', 'selected_subject', 'selected_date', 'students', 'attendanceMap'));
    }

    public function storeAttendance(Request $request)
    {
        $faculty = $this->getFaculty();
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
        ]);

        $subject_id = $request->input('subject_id');
        $date = $request->input('date');
        $attendance_data = $request->input('attendance');

        try {
            DB::beginTransaction();
            foreach ($attendance_data as $student_id => $status) {
                Attendance::updateOrCreate(
                    ['student_id' => $student_id, 'subject_id' => $subject_id, 'date' => $date],
                    ['faculty_id' => $faculty->id, 'status' => $status]
                );
            }
            DB::commit();
            return redirect()->route('faculty.attendance', ['subject_id' => $subject_id, 'date' => $date])
                ->with('success', 'Student attendance marked successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save attendance: ' . $e->getMessage());
        }
    }

    // Student Marks
    public function marks(Request $request)
    {
        $faculty = $this->getFaculty();
        $subjects = Subject::where('faculty_id', $faculty->id)->get();

        $selected_subject = $request->input('subject_id');
        $selected_exam = $request->input('exam_type');

        $students = [];
        $marksMap = collect();

        if ($selected_subject && $selected_exam) {
            $subject = Subject::findOrFail($selected_subject);
            $students = Student::where('course_id', $subject->course_id)
                ->where('current_semester', $subject->semester)
                ->get();

            $marksMap = Mark::where('subject_id', $selected_subject)
                ->where('exam_type', $selected_exam)
                ->get()
                ->keyBy('student_id');
        }

        return view('faculty.marks', compact('subjects', 'selected_subject', 'selected_exam', 'students', 'marksMap'));
    }

    public function storeMarks(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'exam_type' => 'required|in:internal,external,online_exam',
            'marks' => 'required|array',
            'max_marks' => 'required|numeric|min:1',
        ]);

        $subject_id = $request->input('subject_id');
        $exam_type = $request->input('exam_type');
        $max_marks = $request->input('max_marks');
        $marks_data = $request->input('marks');

        try {
            DB::beginTransaction();
            foreach ($marks_data as $student_id => $marks_obtained) {
                if ($marks_obtained === null || $marks_obtained === '') continue;

                Mark::updateOrCreate(
                    ['student_id' => $student_id, 'subject_id' => $subject_id, 'exam_type' => $exam_type],
                    ['marks_obtained' => $marks_obtained, 'max_marks' => $max_marks]
                );
            }
            DB::commit();
            return redirect()->route('faculty.marks', ['subject_id' => $subject_id, 'exam_type' => $exam_type])
                ->with('success', 'Marks saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save marks: ' . $e->getMessage());
        }
    }

    // Assignments CRUD
    public function assignments()
    {
        $faculty = $this->getFaculty();
        $subjects = Subject::where('faculty_id', $faculty->id)->get();
        $assignments = Assignment::with('subject')->where('faculty_id', $faculty->id)->get();
        return view('faculty.assignments.index', compact('assignments', 'subjects'));
    }

    public function storeAssignment(Request $request)
    {
        $faculty = $this->getFaculty();
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'deadline' => 'required|date',
            'file' => 'nullable|file|max:10240', // 10MB
        ]);

        $data = $request->only('title', 'description', 'subject_id', 'deadline');
        $data['faculty_id'] = $faculty->id;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/assignments'), $fileName);
            $data['file_path'] = 'uploads/assignments/' . $fileName;
        }

        Assignment::create($data);
        return redirect()->route('faculty.assignments')->with('success', 'Assignment published successfully!');
    }

    public function deleteAssignment($id)
    {
        $faculty = $this->getFaculty();
        $assignment = Assignment::with('submissions')->where('faculty_id', $faculty->id)->findOrFail($id);

        try {
            DB::beginTransaction();

            // Delete submissions and their files
            foreach ($assignment->submissions as $submission) {
                if ($submission->file_path && file_exists(public_path($submission->file_path))) {
                    @unlink(public_path($submission->file_path));
                }
                $submission->delete();
            }

            // Delete assignment attachment
            if ($assignment->file_path && file_exists(public_path($assignment->file_path))) {
                @unlink(public_path($assignment->file_path));
            }

            $assignment->delete();

            DB::commit();
            return redirect()->route('faculty.assignments')->with('success', 'Assignment and all its submissions deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('faculty.assignments')->with('error', 'Failed to delete assignment: ' . $e->getMessage());
        }
    }

    public function viewAssignmentSubmissions($id)
    {
        $faculty = $this->getFaculty();
        $assignment = Assignment::where('faculty_id', $faculty->id)->findOrFail($id);
        $submissions = Submission::with('student')->where('assignment_id', $assignment->id)->get();

        return view('faculty.assignments.submissions', compact('assignment', 'submissions'));
    }

    public function gradeSubmission(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:submitted,graded,late',
        ]);

        $submission = Submission::findOrFail($id);
        $submission->update(['status' => $request->input('status')]);

        return back()->with('success', 'Submission status updated successfully!');
    }

    // Exams
    public function exams()
    {
        $faculty = $this->getFaculty();
        $departments = \App\Models\Department::orderBy('name')->get();
        $subjects = Subject::with('course.department')->where('faculty_id', $faculty->id)->get();
        $exams = Exam::with('subject')->where('faculty_id', $faculty->id)->get();

        return view('faculty.exams.index', compact('exams', 'subjects', 'departments'));
    }

    public function storeExam(Request $request)
    {
        $faculty = $this->getFaculty();
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'duration_minutes' => 'required|integer|min:5|max:180',
        ]);

        Exam::create([
            'title' => trim($request->input('title')),
            'subject_id' => $request->input('subject_id'),
            'duration_minutes' => $request->input('duration_minutes'),
            'faculty_id' => $faculty->id,
            'status' => 'pending',
        ]);

        return redirect()->route('faculty.exams')->with('success', 'Exam created successfully!');
    }

    public function toggleExamStatus(Request $request, $id)
    {
        $faculty = $this->getFaculty();
        $exam = Exam::where('faculty_id', $faculty->id)->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,active,completed',
        ]);

        $exam->update(['status' => $request->input('status')]);
        return redirect()->route('faculty.exams')->with('success', 'Exam status updated to ' . $request->input('status') . '!');
    }

    public function deleteExam($id)
    {
        $faculty = $this->getFaculty();
        $exam = Exam::where('faculty_id', $faculty->id)->findOrFail($id);
        $exam->delete();

        return redirect()->route('faculty.exams')->with('success', 'Exam deleted successfully!');
    }

    public function manageExam($id)
    {
        $faculty = $this->getFaculty();
        $exam = Exam::with('questions')->where('faculty_id', $faculty->id)->findOrFail($id);

        return view('faculty.exams.questions', compact('exam'));
    }

    public function addQuestion(Request $request, $id)
    {
        $faculty = $this->getFaculty();
        $exam = Exam::where('faculty_id', $faculty->id)->findOrFail($id);

        $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'required|string|max:255',
            'option_d' => 'required|string|max:255',
            'correct_option' => 'required|in:A,B,C,D',
        ]);

        ExamQuestion::create([
            'exam_id' => $exam->id,
            'question_text' => trim($request->input('question_text')),
            'option_a' => trim($request->input('option_a')),
            'option_b' => trim($request->input('option_b')),
            'option_c' => trim($request->input('option_c')),
            'option_d' => trim($request->input('option_d')),
            'correct_option' => $request->input('correct_option'),
        ]);

        return redirect()->route('faculty.exams.manage', $exam->id)->with('success', 'Question added successfully!');
    }

    public function deleteQuestion($id)
    {
        $faculty = $this->getFaculty();
        $question = ExamQuestion::findOrFail($id);
        $exam = Exam::where('faculty_id', $faculty->id)->findOrFail($question->exam_id);

        $question->delete();
        return redirect()->route('faculty.exams.manage', $exam->id)->with('success', 'Question deleted successfully!');
    }

    public function updateQuestion(Request $request, $id)
    {
        $faculty = $this->getFaculty();
        $question = ExamQuestion::findOrFail($id);
        $exam = Exam::where('faculty_id', $faculty->id)->findOrFail($question->exam_id);

        $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'required|string|max:255',
            'option_d' => 'required|string|max:255',
            'correct_option' => 'required|in:A,B,C,D',
        ]);

        $question->update([
            'question_text' => trim($request->input('question_text')),
            'option_a' => trim($request->input('option_a')),
            'option_b' => trim($request->input('option_b')),
            'option_c' => trim($request->input('option_c')),
            'option_d' => trim($request->input('option_d')),
            'correct_option' => $request->input('correct_option'),
        ]);

        return redirect()->route('faculty.exams.manage', $exam->id)->with('success', 'Question updated successfully!');
    }

    public function viewAttempts($id)
    {
        $faculty = $this->getFaculty();
        $exam = Exam::where('faculty_id', $faculty->id)->findOrFail($id);
        $attempts = ExamAttempt::with('student')->where('exam_id', $exam->id)->get();

        return view('faculty.exams.attempts', compact('exam', 'attempts'));
    }

    // Notices
    public function notices()
    {
        $notices = Notice::with(['creator'])
            ->whereNull('role_id')
            ->orWhere('role_id', Auth::user()->role_id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('faculty.notices', compact('notices'));
    }

    // Study Materials
    public function materials()
    {
        $faculty = $this->getFaculty();
        $subjects = Subject::where('faculty_id', $faculty->id)->get();
        $materials = \App\Models\StudyMaterial::with('subject')
            ->where('faculty_id', $faculty->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('faculty.materials', compact('materials', 'subjects'));
    }

    public function storeMaterial(Request $request)
    {
        $faculty = $this->getFaculty();
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'file' => 'required|file|max:20480', // Max 20MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/materials'), $fileName);
            $filePath = 'uploads/materials/' . $fileName;

            \App\Models\StudyMaterial::create([
                'title' => $request->title,
                'description' => $request->description,
                'subject_id' => $request->subject_id,
                'faculty_id' => $faculty->id,
                'file_path' => $filePath,
            ]);

            return redirect()->route('faculty.materials')->with('success', 'Study material uploaded successfully!');
        }

        return back()->with('error', 'Failed to upload study material.');
    }

    public function deleteMaterial($id)
    {
        $faculty = $this->getFaculty();
        $material = \App\Models\StudyMaterial::where('faculty_id', $faculty->id)->findOrFail($id);

        if (file_exists(public_path($material->file_path))) {
            unlink(public_path($material->file_path));
        }

        $material->delete();
        return redirect()->route('faculty.materials')->with('success', 'Study material deleted successfully!');
    }

    // Leave Requests
    public function leave()
    {
        $leaves = \App\Models\LeaveApplication::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('faculty.leave', compact('leaves'));
    }

    public function storeLeave(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'document' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:10240',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ];

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = time() . '_leave_' . Auth::id() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/leaves'), $fileName);
            $data['document_path'] = 'uploads/leaves/' . $fileName;
        }

        \App\Models\LeaveApplication::create($data);

        return redirect()->route('faculty.leave')->with('success', 'Leave application submitted successfully!');
    }

    // Mentoring Records
    public function mentoring()
    {
        $faculty = $this->getFaculty();
        
        $students = Student::whereHas('course', function($q) use ($faculty) {
            $q->where('department_id', $faculty->department_id);
        })->get();

        $records = \App\Models\MentoringRecord::with('student')
            ->where('faculty_id', $faculty->id)
            ->orderBy('meeting_date', 'desc')
            ->get();

        return view('faculty.mentoring', compact('records', 'students'));
    }

    public function storeMentoring(Request $request)
    {
        $faculty = $this->getFaculty();
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'meeting_date' => 'required|date',
            'notes' => 'required|string|max:5000',
        ]);

        \App\Models\MentoringRecord::create([
            'faculty_id' => $faculty->id,
            'student_id' => $request->student_id,
            'meeting_date' => $request->meeting_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('faculty.mentoring')->with('success', 'Mentoring record saved successfully!');
    }

    // Research/Publications Records
    public function research()
    {
        $faculty = $this->getFaculty();
        $publications = \App\Models\ResearchPublication::where('faculty_id', $faculty->id)
            ->orderBy('publication_date', 'desc')
            ->get();
        return view('faculty.research', compact('publications'));
    }

    public function storeResearch(Request $request)
    {
        $faculty = $this->getFaculty();
        $request->validate([
            'title' => 'required|string|max:255',
            'journal_name' => 'required|string|max:255',
            'publication_date' => 'required|date',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB
        ]);

        $data = [
            'faculty_id' => $faculty->id,
            'title' => $request->title,
            'journal_name' => $request->journal_name,
            'publication_date' => $request->publication_date,
            'description' => $request->description,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/research'), $fileName);
            $data['file_path'] = 'uploads/research/' . $fileName;
        }

        \App\Models\ResearchPublication::create($data);

        return redirect()->route('faculty.research')->with('success', 'Research publication record added!');
    }

    // Discussion Forum
    public function forum()
    {
        $topics = \App\Models\ForumTopic::with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('faculty.forum', compact('topics'));
    }

    public function storeForumTopic(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
        ]);

        \App\Models\ForumTopic::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('faculty.forum')->with('success', 'Forum topic created successfully!');
    }

    public function storeForumReply(Request $request)
    {
        $request->validate([
            'topic_id' => 'required|exists:forum_topics,id',
            'reply_text' => 'required|string|max:5000',
        ]);

        \App\Models\ForumReply::create([
            'topic_id' => $request->topic_id,
            'reply_text' => $request->reply_text,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('faculty.forum')->with('success', 'Reply posted successfully!');
    }

    // Messaging/Chat
    public function chat(Request $request)
    {
        $currentUserId = Auth::id();
        $selectedUserId = $request->query('user_id');

        $faculty = $this->getFaculty();
        $students = Student::with('user')
            ->whereHas('course', function($q) use ($faculty) {
                $q->where('department_id', $faculty->department_id);
            })->get();

        $messages = collect();
        $selectedUser = null;

        if ($selectedUserId) {
            $selectedUser = \App\Models\User::findOrFail($selectedUserId);

            $messages = \App\Models\Message::where(function($q) use ($currentUserId, $selectedUserId) {
                $q->where('sender_id', $currentUserId)->where('receiver_id', $selectedUserId);
            })->orWhere(function($q) use ($currentUserId, $selectedUserId) {
                $q->where('sender_id', $selectedUserId)->where('receiver_id', $currentUserId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

            \App\Models\Message::where('sender_id', $selectedUserId)
                ->where('receiver_id', $currentUserId)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return view('faculty.chat', compact('students', 'messages', 'selectedUser'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message_text' => 'required|string|max:5000',
        ]);

        \App\Models\Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message_text' => $request->message_text,
            'is_read' => false,
        ]);

        return redirect()->route('faculty.chat', ['user_id' => $request->receiver_id])->with('success', 'Message sent.');
    }

    public function parseQuestions(Request $request, $exam_id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:10240',
        ]);
        
        $exam = Exam::findOrFail($exam_id);
        $file = $request->file('file');
        
        $apiKey = env('GEMINI_API_KEY');
        if ($apiKey) {
            try {
                $base64 = base64_encode(file_get_contents($file->path()));
                $mimeType = $file->getMimeType();
                
                $prompt = "You are an AI assistant designed to extract multiple-choice questions (MCQs) from exam papers or documents.
                Please parse the provided file and extract at most 10 questions. For each question, identify:
                1. The question text
                2. Option A
                3. Option B
                4. Option C
                5. Option D
                6. The correct option (must be exactly 'A', 'B', 'C', or 'D'). If you cannot find the correct option indicated, make a best guess or select 'A'.
                
                Return the result strictly as a valid JSON array of objects, with no markdown code blocks, no backticks, and no extra text.
                JSON structure:
                [
                  {
                    \"question_text\": \"Question content here\",
                    \"option_a\": \"Text for option A\",
                    \"option_b\": \"Text for option B\",
                    \"option_c\": \"Text for option C\",
                    \"option_d\": \"Text for option D\",
                    \"correct_option\": \"A\"
                  }
                ]";

                // Call Gemini API using HTTP facade
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Content-Type' => 'application/json'
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'inlineData' => [
                                        'mimeType' => $mimeType,
                                        'data' => $base64
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
                ]);

                if ($response->successful()) {
                    $jsonRes = $response->json();
                    $text = $jsonRes['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    
                    // Decode text
                    $questions = json_decode(trim($text), true);
                    if (is_array($questions) && count($questions) > 0) {
                        $questions = array_slice($questions, 0, 10);
                        return response()->json([
                            'success' => true,
                            'questions' => $questions,
                            'source' => 'gemini'
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Gemini API Error: " . $e->getMessage());
            }
        }

        // Mock Fallback: Let's read the subject name of the exam
        $subject = $exam->subject;
        $subjectName = $subject ? $subject->name : 'General';
        
        $mockQuestions = $this->generateMockQuestions($subjectName);
        return response()->json([
            'success' => true,
            'questions' => $mockQuestions,
            'source' => 'mock',
            'warning' => 'Real AI features require a GEMINI_API_KEY in the .env file. Showing parsed questions via simulation.'
        ]);
    }

    private function generateMockQuestions($subjectName)
    {
        $subjectNameLower = strtolower($subjectName);
        if (str_contains($subjectNameLower, 'database') || str_contains($subjectNameLower, 'dbms') || str_contains($subjectNameLower, 'sql')) {
            return [
                [
                    'question_text' => 'Which SQL constraint uniquely identifies each record in a database table?',
                    'option_a' => 'FOREIGN KEY',
                    'option_b' => 'PRIMARY KEY',
                    'option_c' => 'UNIQUE KEY',
                    'option_d' => 'CHECK CONSTRAINT',
                    'correct_option' => 'B'
                ],
                [
                    'question_text' => 'What is the correct order of clauses in a SQL SELECT statement?',
                    'option_a' => 'SELECT, FROM, WHERE, GROUP BY, HAVING, ORDER BY',
                    'option_b' => 'SELECT, WHERE, FROM, GROUP BY, HAVING, ORDER BY',
                    'option_c' => 'SELECT, FROM, GROUP BY, WHERE, HAVING, ORDER BY',
                    'option_d' => 'SELECT, FROM, WHERE, ORDER BY, GROUP BY, HAVING',
                    'correct_option' => 'A'
                ],
                [
                    'question_text' => 'Which normalization form removes transitive functional dependencies?',
                    'option_a' => 'First Normal Form (1NF)',
                    'option_b' => 'Second Normal Form (2NF)',
                    'option_c' => 'Third Normal Form (3NF)',
                    'option_d' => 'Boyce-Codd Normal Form (BCNF)',
                    'correct_option' => 'C'
                ],
                [
                    'question_text' => 'Which SQL command is used to delete data from a database?',
                    'option_a' => 'REMOVE',
                    'option_b' => 'DELETE',
                    'option_c' => 'DROP',
                    'option_d' => 'CLEAR',
                    'correct_option' => 'B'
                ],
                [
                    'question_text' => 'Which SQL operator is used to search for a specified pattern in a column?',
                    'option_a' => 'LIKE',
                    'option_b' => 'MATCH',
                    'option_c' => 'BETWEEN',
                    'option_d' => 'IN',
                    'correct_option' => 'A'
                ],
                [
                    'question_text' => 'What is the default port number for MySQL?',
                    'option_a' => '1521',
                    'option_b' => '3306',
                    'option_c' => '5432',
                    'option_d' => '1433',
                    'correct_option' => 'B'
                ],
                [
                    'question_text' => 'Which clause is used to filter groups in SQL?',
                    'option_a' => 'WHERE',
                    'option_b' => 'HAVING',
                    'option_c' => 'GROUP BY',
                    'option_d' => 'ORDER BY',
                    'correct_option' => 'B'
                ],
                [
                    'question_text' => 'What is a foreign key?',
                    'option_a' => 'A key that uniquely identifies a row in the same table',
                    'option_b' => 'A key used to encrypt the database',
                    'option_c' => 'A column that creates a relationship between two tables',
                    'option_d' => 'A temporary key used for indexing',
                    'correct_option' => 'C'
                ],
                [
                    'question_text' => 'Which join returns all records when there is a match in either left or right table?',
                    'option_a' => 'INNER JOIN',
                    'option_b' => 'LEFT JOIN',
                    'option_c' => 'FULL OUTER JOIN',
                    'option_d' => 'RIGHT JOIN',
                    'correct_option' => 'C'
                ],
                [
                    'question_text' => 'Which SQL aggregate function is used to find the average value?',
                    'option_a' => 'AVG',
                    'option_b' => 'MEAN',
                    'option_c' => 'SUM',
                    'option_d' => 'COUNT',
                    'correct_option' => 'A'
                ]
            ];
        } elseif (str_contains($subjectNameLower, 'java') || str_contains($subjectNameLower, 'oop')) {
            return [
                [
                    'question_text' => 'Which memory management area is used in Java to store objects?',
                    'option_a' => 'Stack Memory',
                    'option_b' => 'Heap Memory',
                    'option_c' => 'Register Memory',
                    'option_d' => 'Static Memory',
                    'correct_option' => 'B'
                ],
                [
                    'question_text' => 'Which OOP concept promotes reusing code and establishing a hierarchical relationship?',
                    'option_a' => 'Encapsulation',
                    'option_b' => 'Polymorphism',
                    'option_c' => 'Inheritance',
                    'option_d' => 'Abstraction',
                    'correct_option' => 'C'
                ],
                [
                    'question_text' => 'What is the default value of a local variable in Java?',
                    'option_a' => 'null',
                    'option_b' => '0',
                    'option_c' => 'Garbage value',
                    'option_d' => 'No default value (must be initialized)',
                    'correct_option' => 'D'
                ],
                [
                    'question_text' => 'Which keyword is used to inherit a class in Java?',
                    'option_a' => 'implements',
                    'option_b' => 'extends',
                    'option_c' => 'inherits',
                    'option_d' => 'import',
                    'correct_option' => 'B'
                ],
                [
                    'question_text' => 'What is the size of double variable in Java?',
                    'option_a' => '2 bytes',
                    'option_b' => '4 bytes',
                    'option_c' => '8 bytes',
                    'option_d' => '16 bytes',
                    'correct_option' => 'C'
                ],
                [
                    'question_text' => 'Which method is the entry point for any Java program?',
                    'option_a' => 'public void main(String[] args)',
                    'option_b' => 'public static void main(String[] args)',
                    'option_c' => 'private static void main(String[] args)',
                    'option_d' => 'static void main()',
                    'correct_option' => 'B'
                ],
                [
                    'question_text' => 'Which package contains the basic classes of Java language?',
                    'option_a' => 'java.util',
                    'option_b' => 'java.io',
                    'option_c' => 'java.lang',
                    'option_d' => 'java.net',
                    'correct_option' => 'C'
                ],
                [
                    'question_text' => 'Can we have multiple main methods in a class in Java?',
                    'option_a' => 'No, compiler will throw error',
                    'option_b' => 'Yes, overloading is allowed',
                    'option_c' => 'Yes, but only one can execute',
                    'option_d' => 'Only in abstract classes',
                    'correct_option' => 'B'
                ],
                [
                    'question_text' => 'Which keyword is used to access parent class constructor or methods?',
                    'option_a' => 'this',
                    'option_b' => 'super',
                    'option_c' => 'parent',
                    'option_d' => 'base',
                    'correct_option' => 'B'
                ],
                [
                    'question_text' => 'What is an abstract class in Java?',
                    'option_a' => 'A class that has only final methods',
                    'option_b' => 'A class that has only static variables',
                    'option_c' => 'A class that cannot be instantiated and may contain abstract methods',
                    'option_d' => 'A class that implements multiple inheritance',
                    'correct_option' => 'C'
                ]
            ];
        } elseif (str_contains($subjectNameLower, 'web') || str_contains($subjectNameLower, 'html') || str_contains($subjectNameLower, 'js') || str_contains($subjectNameLower, 'css')) {
            return [
                [
                    'question_text' => 'Which tag is used in HTML5 for semantic navigation links?',
                    'option_a' => '<nav>',
                    'option_b' => '<navigation>',
                    'option_c' => '<links>',
                    'option_d' => '<menu>',
                    'correct_option' => 'A'
                ],
                [
                    'question_text' => 'What is the correct Javascript syntax to change the content of a paragraph with id="demo"?',
                    'option_a' => 'document.getElement("p").innerHTML = "Hello";',
                    'option_b' => 'document.getElementById("demo").innerHTML = "Hello";',
                    'option_c' => '#demo.innerHTML = "Hello";',
                    'option_d' => 'document.getElementByName("demo").innerHTML = "Hello";',
                    'correct_option' => 'B'
                ],
                [
                    'question_text' => 'What does CSS stand for?',
                    'option_a' => 'Creative Style Sheets',
                    'option_b' => 'Computer Style Sheets',
                    'option_c' => 'Cascading Style Sheets',
                    'option_d' => 'Colorful Style Sheets',
                    'correct_option' => 'C'
                ],
                [
                    'question_text' => 'Which HTML element is used to define important text?',
                    'option_a' => '<strong>',
                    'option_b' => '<important>',
                    'option_c' => '<b>',
                    'option_d' => '<i>',
                    'correct_option' => 'A'
                ],
                [
                    'question_text' => 'In JavaScript, how do you write "Hello World" in an alert box?',
                    'option_a' => 'msgBox("Hello World");',
                    'option_b' => 'alertBox("Hello World");',
                    'option_c' => 'alert("Hello World");',
                    'option_d' => 'console.log("Hello World");',
                    'correct_option' => 'C'
                ],
                [
                    'question_text' => 'What is the default display value of a <div> element?',
                    'option_a' => 'inline',
                    'option_b' => 'block',
                    'option_c' => 'flex',
                    'option_d' => 'none',
                    'correct_option' => 'B'
                ],
                [
                    'question_text' => 'How do you select an element with class name "header" in CSS?',
                    'option_a' => '#header',
                    'option_b' => 'header',
                    'option_c' => '.header',
                    'option_d' => '*header',
                    'correct_option' => 'C'
                ],
                [
                    'question_text' => 'What is the correct way to write a JS array?',
                    'option_a' => 'var colors = "red", "green", "blue"',
                    'option_b' => 'var colors = (1:"red", 2:"green", 3:"blue")',
                    'option_c' => 'var colors = ["red", "green", "blue"]',
                    'option_d' => 'var colors = 1 = ("red"), 2 = ("green")',
                    'correct_option' => 'C'
                ],
                [
                    'question_text' => 'Which HTML5 tag is used to embed audio files?',
                    'option_a' => '<sound>',
                    'option_b' => '<music>',
                    'option_c' => '<audio>',
                    'option_d' => '<play>',
                    'correct_option' => 'C'
                ],
                [
                    'question_text' => 'What property is used in CSS to change the text color?',
                    'option_a' => 'text-color',
                    'option_b' => 'color',
                    'option_c' => 'fgcolor',
                    'option_d' => 'font-color',
                    'correct_option' => 'B'
                ]
            ];
        } else {
            return [
                [
                    'question_text' => 'What is the time complexity of searching an element in a balanced binary search tree?',
                    'option_a' => 'O(1)',
                    'option_b' => 'O(n)',
                    'option_c' => 'O(log n)',
                    'option_d' => 'O(n log n)',
                    'correct_option' => 'C'
                ],
                [
                    'question_text' => 'Which data structure works on the LIFO (Last In First Out) principle?',
                    'option_a' => 'Queue',
                    'option_b' => 'Stack',
                    'option_c' => 'Linked List',
                    'option_d' => 'Heap',
                    'correct_option' => 'B'
                ],
                [
                    'question_text' => 'What is the primary function of an Operating System kernel?',
                    'option_a' => 'Manage system resources and hardware communication',
                    'option_b' => 'Compile source code into machine code',
                    'option_c' => 'Provide a graphical user interface for word processing',
                    'option_d' => 'Scan the system for computer viruses',
                    'correct_option' => 'A'
                ],
                [
                    'question_text' => 'What is the binary equivalent of decimal number 10?',
                    'option_a' => '1001',
                    'option_b' => '1010',
                    'option_c' => '1100',
                    'option_d' => '1110',
                    'correct_option' => 'B'
                ],
                [
                    'question_text' => 'Which CPU scheduling algorithm is non-preemptive?',
                    'option_a' => 'Round Robin',
                    'option_b' => 'Shortest Remaining Time First',
                    'option_c' => 'First-Come First-Served',
                    'option_d' => 'Priority Scheduling (Preemptive)',
                    'correct_option' => 'C'
                ],
                [
                    'question_text' => 'What is the main purpose of DNS in networking?',
                    'option_a' => 'Secure internet traffic',
                    'option_b' => 'Translate domain names to IP addresses',
                    'option_c' => 'Provide dynamic IP addresses to hosts',
                    'option_d' => 'Filter malicious packets',
                    'correct_option' => 'B'
                ],
                [
                    'question_text' => 'Which data structure uses the FIFO (First In First Out) principle?',
                    'option_a' => 'Stack',
                    'option_b' => 'Queue',
                    'option_c' => 'Binary Tree',
                    'option_d' => 'Graph',
                    'correct_option' => 'B'
                ],
                [
                    'question_text' => 'What does HTTP stand for?',
                    'option_a' => 'Hypertext Transfer Protocol',
                    'option_b' => 'Hyperlink Text Transfer Protocol',
                    'option_c' => 'Hosting Transport Terminal Protocol',
                    'option_d' => 'Hypertext Transmission Provider',
                    'correct_option' => 'A'
                ],
                [
                    'question_text' => 'What is the time complexity of QuickSort in the worst case?',
                    'option_a' => 'O(n)',
                    'option_b' => 'O(n log n)',
                    'option_c' => 'O(n^2)',
                    'option_d' => 'O(2^n)',
                    'correct_option' => 'C'
                ],
                [
                    'question_text' => 'Which layer of the OSI model handles routing and packet forwarding?',
                    'option_a' => 'Physical Layer',
                    'option_b' => 'Data Link Layer',
                    'option_c' => 'Network Layer',
                    'option_d' => 'Transport Layer',
                    'correct_option' => 'C'
                ]
            ];
        }
    }

    public function autoGenerateQuestions(Request $request, $exam_id)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'count' => 'required|integer|min:1|max:30',
        ]);
        
        $exam = Exam::findOrFail($exam_id);
        $topic = $request->input('topic');
        $count = $request->input('count', 10);
        
        $apiKey = env('GEMINI_API_KEY');
        if ($apiKey) {
            try {
                $prompt = "You are an AI assistant designed to generate multiple-choice questions (MCQs) for exams.
                Please generate exactly {$count} multiple-choice questions (MCQs) on the topic: '{$topic}' for the exam titled '{$exam->title}'.
                For each question, provide:
                1. The question text
                2. Option A
                3. Option B
                4. Option C
                5. Option D
                6. The correct option (must be exactly 'A', 'B', 'C', or 'D').
                
                Return the result strictly as a valid JSON array of objects, with no markdown code blocks, no backticks, and no extra text.
                JSON structure:
                [
                  {
                    \"question_text\": \"Question content here\",
                    \"option_a\": \"Text for option A\",
                    \"option_b\": \"Text for option B\",
                    \"option_c\": \"Text for option C\",
                    \"option_d\": \"Text for option D\",
                    \"correct_option\": \"A\"
                  }
                ]";

                // Call Gemini API using HTTP facade
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Content-Type' => 'application/json'
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

                if ($response->successful()) {
                    $jsonRes = $response->json();
                    $text = $jsonRes['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    
                    // Decode text
                    $questions = json_decode(trim($text), true);
                    if (is_array($questions) && count($questions) > 0) {
                        return response()->json([
                            'success' => true,
                            'questions' => $questions,
                            'source' => 'gemini'
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Gemini Auto-Generation Error: " . $e->getMessage());
            }
        }

        // Mock Fallback
        $mockQuestions = $this->generateAutomaticMockQuestions($topic, $count);
        return response()->json([
            'success' => true,
            'questions' => $mockQuestions,
            'source' => 'mock',
            'warning' => 'Real AI features require a GEMINI_API_KEY in the .env file. Showing generated questions via simulation.'
        ]);
    }

    private function generateAutomaticMockQuestions($topic, $count)
    {
        $questions = [];
        $topicClean = e($topic);
        for ($i = 1; $i <= $count; $i++) {
            $questions[] = [
                'question_text' => "Simulated AI Question {$i} about '{$topicClean}': Which of the following is correct?",
                'option_a' => "Option A showing key concept of {$topicClean}",
                'option_b' => "Option B representing basic structure",
                'option_c' => "Option C showing advanced application",
                'option_d' => "Option D representing none of the above",
                'correct_option' => ['A', 'B', 'C', 'D'][($i - 1) % 4]
            ];
        }
        return $questions;
    }

    public function importQuestions(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $request->validate([
            'questions' => 'required|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.option_a' => 'required|string',
            'questions.*.option_b' => 'required|string',
            'questions.*.option_c' => 'required|string',
            'questions.*.option_d' => 'required|string',
            'questions.*.correct_option' => 'required|in:A,B,C,D',
        ]);
        
        $importedCount = 0;
        try {
            DB::beginTransaction();
            foreach ($request->input('questions') as $q) {
                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_text' => trim($q['question_text']),
                    'option_a' => trim($q['option_a']),
                    'option_b' => trim($q['option_b']),
                    'option_c' => trim($q['option_c']),
                    'option_d' => trim($q['option_d']),
                    'correct_option' => $q['correct_option'],
                ]);
                $importedCount++;
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "$importedCount questions imported successfully!"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to import questions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function answerSheets()
    {
        $faculty = $this->getFaculty();
        
        $uploads = AnswerSheetUpload::with(['subject', 'evaluatorAssignment.evaluator.user'])
            ->where('uploaded_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        $assignments = EvaluatorAssignment::with(['answerSheet.uploader', 'answerSheet.subject'])
            ->where('evaluator_faculty_id', $faculty->id)
            ->orderBy('assigned_at', 'desc')
            ->get();

        return view('faculty.answer_sheets', compact('uploads', 'assignments'));
    }

    public function uploadAnswerSheet(Request $request, GeminiAIService $geminiService)
    {
        $request->validate([
            'answer_sheet' => 'required|file|mimes:pdf,png,jpg,jpeg|max:20480',
        ]);

        $file = $request->file('answer_sheet');
        $originalFilename = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        $destinationPath = public_path('uploads/answer_sheets');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        
        $file->move($destinationPath, $fileName);
        $filePath = $destinationPath . '/' . $fileName;
        $relativeFilePath = 'uploads/answer_sheets/' . $fileName;

        $upload = AnswerSheetUpload::create([
            'file_path' => $relativeFilePath,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'uploaded_by' => Auth::id(),
            'status' => 'pending',
        ]);

        try {
            $upload->update(['status' => 'processing']);

            $aiResult = $geminiService->analyzeAnswerSheet($filePath, $mimeType);

            if (!$aiResult || !$aiResult['success']) {
                throw new \Exception($aiResult['error'] ?? 'Gemini analysis failed or returned empty response.');
            }

            $data = $aiResult['data'];
            
            AiAuditLog::create([
                'action_type' => 'answer_sheet_process',
                'user_id' => Auth::id(),
                'target_table' => 'answer_sheet_uploads',
                'target_id' => $upload->id,
                'details' => json_encode($data),
                'status' => 'success'
            ]);

            $subject = null;
            if (!empty($data['subject_code'])) {
                $subject = Subject::where('code', trim($data['subject_code']))->first();
            }
            if (!$subject && !empty($data['subject_name'])) {
                $subject = Subject::where('name', 'LIKE', '%' . trim($data['subject_name']) . '%')->first();
            }

            $upload->update([
                'detected_subject' => $data['subject_name'] ?? null,
                'detected_subject_code' => $data['subject_code'] ?? null,
                'detected_department' => $data['department'] ?? null,
                'detected_exam_type' => $data['exam_type'] ?? null,
                'detected_semester' => $data['semester'] ?? null,
                'detected_student_info' => $data['student_name'] ? ($data['student_name'] . ' (' . ($data['student_enrollment'] ?? 'N/A') . ')') : null,
                'detected_date' => $data['exam_date'] ?? null,
                'subject_id' => $subject ? $subject->id : null,
                'ai_confidence_score' => $data['confidence_score'] ?? null,
                'ai_raw_response' => json_encode($data),
                'ai_source' => $aiResult['source'] ?? 'gemini',
            ]);

            if (!$subject) {
                $upload->update(['status' => 'review_needed', 'error_message' => 'Subject could not be matched automatically.']);
                
                AppNotification::create([
                    'user_id' => Auth::id(),
                    'title' => 'Answer Sheet Match Failed',
                    'message' => "Uploaded file '{$originalFilename}' was processed but the subject could not be matched automatically. Manual review is required.",
                    'type' => 'warning',
                    'icon' => 'fas fa-exclamation-triangle',
                    'link' => route('faculty.answer-sheets')
                ]);

                return response()->json([
                    'success' => true,
                    'status' => 'review_needed',
                    'message' => 'Processed but subject match failed. Needs manual review.',
                    'data' => $upload
                ]);
            }

            $uploaderFaculty = Faculty::where('user_id', Auth::id())->first();
            $uploaderFacultyId = $uploaderFaculty ? $uploaderFaculty->id : null;
            $subjectDepartmentId = $subject->course ? $subject->course->department_id : null;

            $faculties = Faculty::where('user_id', '!=', Auth::id())->get();
            $candidates = [];

            foreach ($faculties as $cand) {
                $score = 0;
                $reasons = [];

                if ($cand->id === $subject->faculty_id) {
                    $score += 10;
                    $reasons[] = "Subject expertise";
                }

                if ($subjectDepartmentId && $cand->department_id === $subjectDepartmentId) {
                    $score += 5;
                    $reasons[] = "Department match";
                }

                $activeWorkload = EvaluatorAssignment::where('evaluator_faculty_id', $cand->id)
                    ->whereIn('status', ['assigned', 'in_progress'])
                    ->count();
                $score -= $activeWorkload;
                if ($activeWorkload > 0) {
                    $reasons[] = "Workload penalty (-{$activeWorkload} tasks)";
                }

                $isOnLeave = LeaveApplication::where('user_id', $cand->user_id)
                    ->where('status', 'approved')
                    ->whereDate('start_date', '<=', today())
                    ->whereDate('end_date', '>=', today())
                    ->exists();
                if ($isOnLeave) {
                    $score -= 100;
                    $reasons[] = "On approved leave";
                }

                $candidates[] = [
                    'faculty' => $cand,
                    'score' => $score,
                    'reasons' => implode(', ', $reasons),
                    'active_workload' => $activeWorkload,
                    'is_on_leave' => $isOnLeave
                ];
            }

            usort($candidates, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            $bestCandidate = !empty($candidates) ? $candidates[0] : null;

            if (!$bestCandidate || $bestCandidate['is_on_leave'] || $bestCandidate['score'] < -50) {
                $upload->update(['status' => 'review_needed', 'error_message' => 'No available evaluator could be found.']);
                
                AppNotification::create([
                    'user_id' => Auth::id(),
                    'title' => 'Evaluator Assignment Failed',
                    'message' => "No suitable evaluator could be automatically assigned for '{$originalFilename}'.",
                    'type' => 'warning',
                    'icon' => 'fas fa-user-times',
                    'link' => route('faculty.answer-sheets')
                ]);

                return response()->json([
                    'success' => true,
                    'status' => 'review_needed',
                    'message' => 'Processed but no available evaluator found. Needs manual review.',
                    'data' => $upload
                ]);
            }

            $assignment = EvaluatorAssignment::create([
                'answer_sheet_id' => $upload->id,
                'evaluator_faculty_id' => $bestCandidate['faculty']->id,
                'assigned_by_ai' => true,
                'assignment_reason' => $bestCandidate['reasons'],
                'assignment_score' => $bestCandidate['score'],
                'status' => 'assigned'
            ]);

            $upload->update(['status' => 'assigned']);

            AiAuditLog::create([
                'action_type' => 'evaluator_assign',
                'user_id' => Auth::id(),
                'target_table' => 'evaluator_assignments',
                'target_id' => $assignment->id,
                'details' => json_encode([
                    'subject_id' => $subject->id,
                    'evaluator_faculty_id' => $bestCandidate['faculty']->id,
                    'score' => $bestCandidate['score'],
                    'reason' => $bestCandidate['reasons']
                ]),
                'status' => 'success'
            ]);

            AppNotification::create([
                'user_id' => Auth::id(),
                'title' => 'Answer Sheet Allocated',
                'message' => "Uploaded answer sheet '{$originalFilename}' (Subject: {$subject->name}) has been assigned to {$bestCandidate['faculty']->first_name} {$bestCandidate['faculty']->last_name}.",
                'type' => 'success',
                'icon' => 'fas fa-check-circle',
                'link' => route('faculty.answer-sheets')
            ]);

            AppNotification::create([
                'user_id' => $bestCandidate['faculty']->user_id,
                'title' => 'New Answer Sheet for Evaluation',
                'message' => "You have been assigned to evaluate a new answer sheet for {$subject->name}.",
                'type' => 'info',
                'icon' => 'fas fa-file-signature',
                'link' => route('faculty.answer-sheets')
            ]);

            return response()->json([
                'success' => true,
                'status' => 'assigned',
                'message' => "Answer sheet successfully processed and allocated!",
                'data' => [
                    'upload' => $upload,
                    'assignment' => $assignment,
                    'evaluator_name' => $bestCandidate['faculty']->first_name . ' ' . $bestCandidate['faculty']->last_name
                ]
            ]);

        } catch (\Exception $e) {
            $upload->update(['status' => 'failed', 'error_message' => $e->getMessage()]);

            AiAuditLog::create([
                'action_type' => 'answer_sheet_process',
                'user_id' => Auth::id(),
                'target_table' => 'answer_sheet_uploads',
                'target_id' => $upload->id,
                'details' => json_encode(['error' => $e->getMessage()]),
                'status' => 'failure',
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Processing failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
