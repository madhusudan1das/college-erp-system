<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Attendance;
use App\Models\Mark;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Models\Notice;
use App\Models\Timetable;

class StudentController extends Controller
{
    private function getStudent()
    {
        return Student::where('user_id', Auth::id())->firstOrFail();
    }

    // Dashboard
    public function dashboard()
    {
        $student = $this->getStudent();

        $subjects_count = Subject::where('course_id', $student->course_id)
            ->where('semester', $student->current_semester)
            ->count();

        $assignments_count = Assignment::where('subject_id', function ($query) use ($student) {
                $query->select('id')
                    ->from('subjects')
                    ->where('course_id', $student->course_id)
                    ->where('semester', $student->current_semester)
                    ->limit(1);
            })->count();

        $exams_count = Exam::where('status', 'active')
            ->whereIn('subject_id', function ($query) use ($student) {
                $query->select('id')
                    ->from('subjects')
                    ->where('course_id', $student->course_id)
                    ->where('semester', $student->current_semester);
            })->count();

        // Notices
        $notices = Notice::with('creator')
            ->whereNull('role_id')
            ->orWhere('role_id', Auth::user()->role_id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('student.dashboard', compact('student', 'subjects_count', 'assignments_count', 'exams_count', 'notices'));
    }

    // Attendance
    public function attendance()
    {
        $student = $this->getStudent();
        
        $attendance = Attendance::with('subject')
            ->where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->get();

        // Stats
        $stats = [
            'total' => $attendance->count(),
            'present' => $attendance->where('status', 'present')->count(),
            'absent' => $attendance->where('status', 'absent')->count(),
            'late' => $attendance->where('status', 'late')->count(),
        ];

        return view('student.attendance', compact('attendance', 'stats'));
    }

    // Results
    public function results()
    {
        $student = $this->getStudent();
        
        $results = Mark::with('subject')
            ->where('student_id', $student->id)
            ->get();

        return view('student.results', compact('results'));
    }

    // Assignments
    public function assignments()
    {
        $student = $this->getStudent();

        // Get subjects in this semester
        $subjectIds = Subject::where('course_id', $student->course_id)
            ->where('semester', $student->current_semester)
            ->pluck('id');

        $assignments = Assignment::with(['subject', 'submissions' => function($q) use ($student) {
            $q->where('student_id', $student->id);
        }])
        ->whereIn('subject_id', $subjectIds)
        ->get();

        return view('student.assignments', compact('assignments'));
    }

    public function submitAssignment(Request $request, $id)
    {
        $student = $this->getStudent();
        $assignment = Assignment::findOrFail($id);

        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $student->enrollment_no . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/submissions'), $fileName);
            $filePath = 'uploads/submissions/' . $fileName;

            // Determine status
            $status = now()->gt($assignment->deadline) ? 'late' : 'submitted';

            Submission::updateOrCreate(
                ['assignment_id' => $assignment->id, 'student_id' => $student->id],
                ['file_path' => $filePath, 'status' => $status]
            );

            return redirect()->route('student.assignments')->with('success', 'Assignment submitted successfully!');
        }

        return back()->with('error', 'Failed to upload file.');
    }

    // Timetable
    public function timetable()
    {
        $student = $this->getStudent();
        
        $timetable = Timetable::with(['subject', 'faculty'])
            ->where('course_id', $student->course_id)
            ->where('semester', $student->current_semester)
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

        return view('student.timetable', compact('timetable', 'days', 'conflicts', 'conflictIds'));
    }

    // Exams
    public function exams()
    {
        $student = $this->getStudent();

        $subjectIds = Subject::where('course_id', $student->course_id)
            ->where('semester', $student->current_semester)
            ->pluck('id');

        // Fetch active exams and attempts
        $exams = Exam::with('subject')
            ->where('status', 'active')
            ->whereIn('subject_id', $subjectIds)
            ->get();

        $attempts = ExamAttempt::where('student_id', $student->id)
            ->get()
            ->keyBy('exam_id');

        return view('student.exams', compact('exams', 'attempts'));
    }

    public function takeExam($id)
    {
        $student = $this->getStudent();
        $exam = Exam::where('status', 'active')->findOrFail($id);

        // Check if student has already completed the exam
        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        if ($attempt && $attempt->status == 'submitted') {
            return redirect()->route('student.exams')->with('error', 'You have already completed this exam.');
        }

        $questions = ExamQuestion::where('exam_id', $exam->id)->get();
        $total_questions = $questions->count();

        if (!$attempt) {
            // Create ongoing attempt
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'start_time' => now(),
                'total_questions' => $total_questions,
                'status' => 'ongoing',
            ]);
        }

        // Calculate time remaining
        $startTime = $attempt->start_time;
        $durationSeconds = $exam->duration_minutes * 60;
        $timeElapsed = now()->timestamp - $startTime->timestamp;
        $time_remaining = max(0, $durationSeconds - $timeElapsed);

        if ($time_remaining <= 0) {
            // Force submit the exam
            $attempt->update([
                'end_time' => now(),
                'status' => 'submitted',
            ]);
            return redirect()->route('student.exams.result', $exam->id)->with('error', 'Time was up! Exam auto-submitted.');
        }

        return view('student.take_exam', compact('exam', 'attempt', 'questions', 'time_remaining'));
    }

    public function submitExam(Request $request, $id)
    {
        $student = $this->getStudent();
        $exam = Exam::findOrFail($id);

        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        if ($attempt->status == 'submitted') {
            return redirect()->route('student.exams')->with('error', 'You have already completed this exam.');
        }

        $answers = $request->input('answers', []);
        $score = 0;

        try {
            DB::beginTransaction();

            foreach ($answers as $q_id => $selected_opt) {
                $question = ExamQuestion::findOrFail($q_id);
                $is_correct = ($selected_opt === $question->correct_option) ? 1 : 0;
                $score += $is_correct;

                ExamAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $q_id,
                    'selected_option' => $selected_opt,
                    'is_correct' => $is_correct,
                ]);
            }

            $total_questions = ExamQuestion::where('exam_id', $exam->id)->count();

            // Update attempt
            $attempt->update([
                'end_time' => now(),
                'score' => $score,
                'total_questions' => $total_questions,
                'status' => 'submitted',
            ]);

            // Sync with marks table
            Mark::updateOrCreate(
                ['student_id' => $student->id, 'subject_id' => $exam->subject_id, 'exam_type' => 'online_exam'],
                ['marks_obtained' => $score, 'max_marks' => $total_questions]
            );

            DB::commit();
            return redirect()->route('student.exams.result', $exam->id)->with('success', 'Exam completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit exam: ' . $e->getMessage());
        }
    }

    public function examResult($id)
    {
        $student = $this->getStudent();
        $exam = Exam::findOrFail($id);

        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->where('status', 'submitted')
            ->firstOrFail();

        // Get all questions for this exam
        $questions = ExamQuestion::where('exam_id', $exam->id)->get();

        // Get the student's answers for this attempt
        $answers = ExamAnswer::where('attempt_id', $attempt->id)
            ->get()
            ->keyBy('question_id');

        return view('student.exam_result', compact('exam', 'attempt', 'questions', 'answers'));
    }

    // Study Materials
    public function materials()
    {
        $student = $this->getStudent();
        $subjectIds = Subject::where('course_id', $student->course_id)
            ->where('semester', $student->current_semester)
            ->pluck('id');
        $materials = \App\Models\StudyMaterial::with(['subject', 'faculty'])
            ->whereIn('subject_id', $subjectIds)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('student.materials', compact('materials'));
    }

    // Leave Applications
    public function leave()
    {
        $leaves = \App\Models\LeaveApplication::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('student.leave', compact('leaves'));
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

        return redirect()->route('student.leave')->with('success', 'Leave application submitted successfully!');
    }

    // Grievances/Complaints
    public function complaints()
    {
        $complaints = \App\Models\Complaint::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('student.complaints', compact('complaints'));
    }

    public function storeComplaint(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
        ]);

        \App\Models\Complaint::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return redirect()->route('student.complaints')->with('success', 'Grievance submitted successfully!');
    }

    // Fees Portal
    public function fees()
    {
        $student = $this->getStudent();
        $fees = \App\Models\Fee::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('student.fees', compact('fees'));
    }

    public function payFee(Request $request, $id)
    {
        $student = $this->getStudent();
        $fee = \App\Models\Fee::where('student_id', $student->id)->findOrFail($id);

        if ($fee->status === 'paid') {
            return back()->with('error', 'Fee already paid.');
        }

        $fee->update([
            'status' => 'paid',
            'paid_at' => now(),
            'transaction_no' => 'TXN' . time() . rand(100, 999),
        ]);

        return redirect()->route('student.fees')->with('success', 'Fee paid successfully! Transaction ID: ' . $fee->transaction_no);
    }

    public function viewReceipt($id)
    {
        $student = $this->getStudent();
        $fee = \App\Models\Fee::where('student_id', $student->id)
            ->where('status', 'paid')
            ->findOrFail($id);

        return view('student.receipt', compact('student', 'fee'));
    }

    // Campus Services
    public function services()
    {
        $student = $this->getStudent();
        
        $hostelAllotment = \App\Models\HostelAllotment::with('hostel')
            ->where('student_id', $student->id)
            ->first();

        $transportAllotment = \App\Models\TransportAllotment::with('transport')
            ->where('student_id', $student->id)
            ->first();

        $booksIssued = \App\Models\BookIssue::with('book')
            ->where('user_id', Auth::id())
            ->get();

        return view('student.services', compact('student', 'hostelAllotment', 'transportAllotment', 'booksIssued'));
    }

    // Discussion Forum
    public function forum()
    {
        $topics = \App\Models\ForumTopic::with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('student.forum', compact('topics'));
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

        return redirect()->route('student.forum')->with('success', 'Forum topic created successfully!');
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

        return redirect()->route('student.forum')->with('success', 'Reply posted successfully!');
    }

    // Messaging/Chat
    public function chat(Request $request)
    {
        $currentUserId = Auth::id();
        $selectedUserId = $request->query('user_id');

        $faculty = \App\Models\Faculty::with('user')->get();

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

        return view('student.chat', compact('faculty', 'messages', 'selectedUser'));
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

        return redirect()->route('student.chat', ['user_id' => $request->receiver_id])->with('success', 'Message sent.');
    }
}
