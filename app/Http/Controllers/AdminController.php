<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\Course;
use App\Models\Department;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\Notice;
use App\Models\FacultyAttendance;
use App\Models\TimetableUpload;
use App\Models\AppNotification;
use App\Models\AiAuditLog;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Services\GeminiAIService;

class AdminController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        $total_students = Student::count();
        $total_faculty = Faculty::count();
        $total_courses = Course::count();
        $total_departments = Department::count();

        return view('admin.dashboard', compact('total_students', 'total_faculty', 'total_courses', 'total_departments'));
    }

    // Students CRUD
    public function students(Request $request)
    {
        $departments = Department::orderBy('name')->get();
        
        $query = Student::with(['course.department', 'user']);
        
        if ($request->filled('department_id')) {
            $query->whereHas('course', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        
        if ($request->filled('semester')) {
            $query->where('current_semester', $request->semester);
        }
        
        $students = $query->get();
        return view('admin.students.index', compact('students', 'departments'));
    }

    public function addStudentForm()
    {
        $courses = Course::all();
        return view('admin.students.create', compact('courses'));
    }

    public function addStudent(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'enrollment_no' => 'required|string|unique:students,enrollment_no',
            'course_id' => 'required|exists:courses,id',
            'current_semester' => 'required|integer|min:1|max:10',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
        ]);

        try {
            DB::beginTransaction();

            $role = Role::where('name', 'student')->first();

            $user = User::create([
                'username' => trim($request->input('username')),
                'password' => Hash::make(trim($request->input('password'))),
                'email' => trim($request->input('email')),
                'role_id' => $role->id,
            ]);

            Student::create([
                'user_id' => $user->id,
                'first_name' => trim($request->input('first_name')),
                'last_name' => trim($request->input('last_name')),
                'enrollment_no' => trim($request->input('enrollment_no')),
                'course_id' => $request->input('course_id'),
                'current_semester' => $request->input('current_semester'),
            ]);

            DB::commit();
            return redirect()->route('admin.students')->with('success', 'Student added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add student: ' . $e->getMessage())->withInput();
        }
    }

    public function editStudentForm($id)
    {
        $student = Student::with('user')->findOrFail($id);
        $courses = Course::all();
        return view('admin.students.edit', compact('student', 'courses'));
    }

    public function editStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $user = User::findOrFail($student->user_id);

        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'enrollment_no' => 'required|string|unique:students,enrollment_no,' . $student->id,
            'course_id' => 'required|exists:courses,id',
            'current_semester' => 'required|integer|min:1|max:10',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
        ]);

        try {
            DB::beginTransaction();

            $user->username = trim($request->input('username'));
            $user->email = trim($request->input('email'));
            if ($request->filled('password')) {
                $user->password = Hash::make(trim($request->input('password')));
            }
            $user->save();

            $student->update([
                'first_name' => trim($request->input('first_name')),
                'last_name' => trim($request->input('last_name')),
                'enrollment_no' => trim($request->input('enrollment_no')),
                'course_id' => $request->input('course_id'),
                'current_semester' => $request->input('current_semester'),
            ]);

            DB::commit();
            return redirect()->route('admin.students')->with('success', 'Student updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update student: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteStudent($id)
    {
        $student = Student::findOrFail($id);
        $user = User::findOrFail($student->user_id);

        try {
            DB::beginTransaction();
            $student->delete();
            $user->delete();
            DB::commit();
            return redirect()->route('admin.students')->with('success', 'Student deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.students')->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }

    // Faculty CRUD
    public function faculty(Request $request)
    {
        $departments = Department::orderBy('name')->get();
        
        $query = Faculty::with(['department', 'user']);
        
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        $facultyList = $query->get();
        return view('admin.faculty.index', compact('facultyList', 'departments'));
    }

    public function addFacultyForm()
    {
        $departments = Department::all();
        return view('admin.faculty.create', compact('departments'));
    }

    public function addFaculty(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'department_id' => 'required|exists:departments,id',
            'phone' => 'nullable|string|max:20',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
        ]);

        try {
            DB::beginTransaction();

            $role = Role::where('name', 'faculty')->first();

            $user = User::create([
                'username' => trim($request->input('username')),
                'password' => Hash::make(trim($request->input('password'))),
                'email' => trim($request->input('email')),
                'role_id' => $role->id,
            ]);

            Faculty::create([
                'user_id' => $user->id,
                'first_name' => trim($request->input('first_name')),
                'last_name' => trim($request->input('last_name')),
                'department_id' => $request->input('department_id'),
                'phone' => trim($request->input('phone')),
            ]);

            DB::commit();
            return redirect()->route('admin.faculty')->with('success', 'Faculty member added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add faculty: ' . $e->getMessage())->withInput();
        }
    }

    public function editFacultyForm($id)
    {
        $faculty = Faculty::with('user')->findOrFail($id);
        $departments = Department::all();
        return view('admin.faculty.edit', compact('faculty', 'departments'));
    }

    public function editFaculty(Request $request, $id)
    {
        $faculty = Faculty::findOrFail($id);
        $user = User::findOrFail($faculty->user_id);

        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'department_id' => 'required|exists:departments,id',
            'phone' => 'nullable|string|max:20',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
        ]);

        try {
            DB::beginTransaction();

            $user->username = trim($request->input('username'));
            $user->email = trim($request->input('email'));
            if ($request->filled('password')) {
                $user->password = Hash::make(trim($request->input('password')));
            }
            $user->save();

            $faculty->update([
                'first_name' => trim($request->input('first_name')),
                'last_name' => trim($request->input('last_name')),
                'department_id' => $request->input('department_id'),
                'phone' => trim($request->input('phone')),
            ]);

            DB::commit();
            return redirect()->route('admin.faculty')->with('success', 'Faculty updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update faculty: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteFaculty($id)
    {
        $faculty = Faculty::findOrFail($id);
        $user = User::findOrFail($faculty->user_id);

        try {
            DB::beginTransaction();
            $faculty->delete();
            $user->delete();
            DB::commit();
            return redirect()->route('admin.faculty')->with('success', 'Faculty member deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.faculty')->with('error', 'Failed to delete faculty: ' . $e->getMessage());
        }
    }

    // Courses CRUD
    public function courses()
    {
        $courses = Course::with('department')->get();
        $departments = Department::all();
        return view('admin.courses', compact('courses', 'departments'));
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'duration_years' => 'required|integer|min:1|max:10',
        ]);

        Course::create($request->all());
        return redirect()->route('admin.courses')->with('success', 'Course added successfully!');
    }

    public function deleteCourse($id)
    {
        try {
            $course = Course::findOrFail($id);
            $course->delete();
            return redirect()->route('admin.courses')->with('success', 'Course deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.courses')->with('error', 'Cannot delete course. It may have associated students/subjects.');
        }
    }

    // Departments CRUD
    public function departments()
    {
        $departments = Department::all();
        return view('admin.departments', compact('departments'));
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:departments,code',
        ]);

        Department::create($request->all());
        return redirect()->route('admin.departments')->with('success', 'Department added successfully!');
    }

    public function deleteDepartment($id)
    {
        try {
            $dept = Department::findOrFail($id);
            $dept->delete();
            return redirect()->route('admin.departments')->with('success', 'Department deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.departments')->with('error', 'Cannot delete department. It may have associated faculty/courses.');
        }
    }

    // Subjects CRUD
    public function subjects()
    {
        $subjects = Subject::with(['course', 'faculty'])->get();
        $courses = Course::all();
        $facultyList = Faculty::all();
        return view('admin.subjects', compact('subjects', 'courses', 'facultyList'));
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:subjects,code',
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:10',
            'faculty_id' => 'nullable|exists:faculty,id',
        ]);

        Subject::create($request->all());
        return redirect()->route('admin.subjects')->with('success', 'Subject added successfully!');
    }

    public function deleteSubject($id)
    {
        try {
            $subject = Subject::findOrFail($id);
            $subject->delete();
            return redirect()->route('admin.subjects')->with('success', 'Subject deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.subjects')->with('error', 'Cannot delete subject.');
        }
    }

    // Timetable CRUD
    public function timetable()
    {
        $timetables = Timetable::with(['department', 'course', 'subject', 'faculty'])->get();
        $departments = Department::all();
        $courses = Course::all();
        $subjects = Subject::all();
        $facultyList = Faculty::all();

        // Detect faculty conflicts (same faculty, same day, overlapping time)
        $facultyConflicts = [];
        $studentConflicts = [];
        $grouped = $timetables->groupBy('faculty_id');
        foreach ($grouped as $facultyId => $slots) {
            $slotArr = $slots->values()->all();
            for ($i = 0; $i < count($slotArr); $i++) {
                for ($j = $i + 1; $j < count($slotArr); $j++) {
                    $a = $slotArr[$i];
                    $b = $slotArr[$j];
                    if ($a->day_of_week === $b->day_of_week && $a->start_time < $b->end_time && $a->end_time > $b->start_time) {
                        $facultyConflicts[] = [
                            'faculty' => $a->faculty,
                            'slot_a' => $a,
                            'slot_b' => $b,
                        ];
                    }
                }
            }
        }

        // Detect student conflicts (same course+semester, same day, overlapping time)
        $courseGroups = $timetables->groupBy(function ($t) {
            return $t->course_id . '-' . $t->semester;
        });
        foreach ($courseGroups as $key => $slots) {
            $slotArr = $slots->values()->all();
            for ($i = 0; $i < count($slotArr); $i++) {
                for ($j = $i + 1; $j < count($slotArr); $j++) {
                    $a = $slotArr[$i];
                    $b = $slotArr[$j];
                    if ($a->day_of_week === $b->day_of_week && $a->start_time < $b->end_time && $a->end_time > $b->start_time) {
                        $studentConflicts[] = [
                            'course' => $a->course,
                            'semester' => $a->semester,
                            'slot_a' => $a,
                            'slot_b' => $b,
                        ];
                    }
                }
            }
        }

        // Collect IDs of conflicting timetable entries for highlighting
        $conflictIds = collect();
        foreach ($facultyConflicts as $c) {
            $conflictIds->push($c['slot_a']->id);
            $conflictIds->push($c['slot_b']->id);
        }
        foreach ($studentConflicts as $c) {
            $conflictIds->push($c['slot_a']->id);
            $conflictIds->push($c['slot_b']->id);
        }
        $conflictIds = $conflictIds->unique()->values();

        return view('admin.timetable', compact('timetables', 'departments', 'courses', 'subjects', 'facultyList', 'facultyConflicts', 'studentConflicts', 'conflictIds'));
    }

    public function storeTimetable(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:10',
            'subject_id' => 'required|exists:subjects,id',
            'faculty_id' => 'required|exists:faculty,id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        Timetable::create($request->all());
        return redirect()->route('admin.timetable')->with('success', 'Timetable slot created successfully!');
    }

    public function deleteTimetable($id)
    {
        $timetable = Timetable::findOrFail($id);
        $timetable->delete();
        return redirect()->route('admin.timetable')->with('success', 'Timetable slot deleted successfully!');
    }

    public function updateTimetable(Request $request, $id)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:10',
            'subject_id' => 'required|exists:subjects,id',
            'faculty_id' => 'required|exists:faculty,id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room' => 'nullable|string|max:50',
        ]);

        $timetable = Timetable::findOrFail($id);
        $timetable->update($request->all());

        return redirect()->route('admin.timetable')->with('success', 'Timetable slot updated successfully!');
    }

    // Notices CRUD
    public function notices()
    {
        $notices = Notice::with(['role', 'creator'])->orderBy('created_at', 'desc')->get();
        $roles = Role::all();
        return view('admin.notices', compact('notices', 'roles'));
    }

    public function storeNotice(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'role_id' => 'nullable|exists:roles,id',
            'attachment' => 'nullable|file|max:5120', // Max 5MB
        ]);

        $data = $request->only('title', 'content', 'role_id');
        $data['created_by'] = Auth::id();

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/notices'), $fileName);
            $data['attachment'] = 'uploads/notices/' . $fileName;
        }

        Notice::create($data);
        return redirect()->route('admin.notices')->with('success', 'Notice published successfully!');
    }

    public function deleteNotice($id)
    {
        $notice = Notice::findOrFail($id);
        if ($notice->attachment && file_exists(public_path($notice->attachment))) {
            unlink(public_path($notice->attachment));
        }
        $notice->delete();
        return redirect()->route('admin.notices')->with('success', 'Notice deleted successfully!');
    }

    // Faculty Attendance
    public function facultyAttendance(Request $request)
    {
        $selected_date = $request->input('date', date('Y-m-d'));
        $active_tab = $request->input('tab', 'mark');

        $faculty_list = Faculty::with(['department', 'user'])->get();

        // Stats
        $stats = [
            'total' => count($faculty_list),
            'present' => 0,
            'absent' => 0,
            'late' => 0,
        ];

        $counts = FacultyAttendance::where('date', $selected_date)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $stats['present'] = $counts->get('present', 0);
        $stats['absent'] = $counts->get('absent', 0);
        $stats['late'] = $counts->get('late', 0);

        // Preload status map
        $attendanceMap = FacultyAttendance::where('date', $selected_date)
            ->pluck('status', 'faculty_id');

        // History
        $history = FacultyAttendance::with(['faculty.department'])
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.faculty_attendance', compact(
            'selected_date', 'active_tab', 'faculty_list', 'stats', 'attendanceMap', 'history'
        ));
    }

    public function storeFacultyAttendance(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'attendance' => 'required|array',
        ]);

        $date = $request->input('date');
        $attendance_data = $request->input('attendance');

        try {
            DB::beginTransaction();
            foreach ($attendance_data as $faculty_id => $status) {
                FacultyAttendance::updateOrCreate(
                    ['faculty_id' => $faculty_id, 'date' => $date],
                    ['status' => $status]
                );
            }
            DB::commit();
            return redirect()->route('admin.faculty-attendance', ['date' => $date, 'tab' => 'mark'])
                ->with('success', 'Faculty attendance for ' . date('d M Y', strtotime($date)) . ' saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save attendance: ' . $e->getMessage());
        }
    }

    // Department-Wise Directory
    public function departmentWise(Request $request)
    {
        $departments = Department::orderBy('name')->get();
        $selected_dept_id = $request->input('department_id', $departments->first()?->id);

        $selected_dept = null;
        $faculty_list = collect();
        $student_list = collect();

        if ($selected_dept_id) {
            $selected_dept = Department::findOrFail($selected_dept_id);
            $faculty_list = Faculty::with('user')->where('department_id', $selected_dept_id)->get();
            $student_list = $selected_dept->students()->with(['course', 'user'])->get();
        }

        return view('admin.department_wise', compact('departments', 'selected_dept_id', 'selected_dept', 'faculty_list', 'student_list'));
    }

    // Leave Requests Management
    public function leaves()
    {
        $leaves = \App\Models\LeaveApplication::with(['user.student', 'user.faculty', 'user.role'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.leaves', compact('leaves'));
    }

    public function approveLeave(Request $request, $id)
    {
        $leave = \App\Models\LeaveApplication::findOrFail($id);
        $leave->update([
            'status' => 'approved',
            'comments' => $request->input('comments'),
        ]);
        return redirect()->route('admin.leaves')->with('success', 'Leave application approved.');
    }

    public function rejectLeave(Request $request, $id)
    {
        $leave = \App\Models\LeaveApplication::findOrFail($id);
        $leave->update([
            'status' => 'rejected',
            'comments' => $request->input('comments'),
        ]);
        return redirect()->route('admin.leaves')->with('success', 'Leave application rejected.');
    }

    // Grievances/Complaints Management
    public function complaints()
    {
        $complaints = \App\Models\Complaint::with(['user.student', 'user.faculty', 'user.role'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.complaints', compact('complaints'));
    }

    public function resolveComplaint(Request $request, $id)
    {
        $request->validate([
            'resolution' => 'required|string|max:5000',
        ]);

        $complaint = \App\Models\Complaint::findOrFail($id);
        $complaint->update([
            'status' => 'resolved',
            'resolution' => $request->resolution,
        ]);

        return redirect()->route('admin.complaints')->with('success', 'Grievance status resolved successfully.');
    }

    // Fees Manager
    public function fees()
    {
        $students = Student::with(['course.department'])->get();
        $departments = Department::orderBy('name')->get();
        $fees = \App\Models\Fee::with('student.course')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.fees', compact('students', 'fees', 'departments'));
    }

    public function storeFeeDues(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|exists:students,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            foreach ($request->input('student_ids') as $student_id) {
                \App\Models\Fee::create([
                    'student_id' => $student_id,
                    'title' => trim($request->input('title')),
                    'amount' => $request->input('amount'),
                    'status' => 'unpaid',
                ]);
            }
            DB::commit();
            return redirect()->route('admin.fees')->with('success', 'Fee dues generated successfully for the selected students!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to generate fee dues: ' . $e->getMessage())->withInput();
        }
    }

    // Payroll Manager
    public function payroll()
    {
        $faculty = Faculty::with('user')->get();
        $salaries = \App\Models\Salary::with('faculty')->orderBy('pay_date', 'desc')->get();
        return view('admin.payroll', compact('faculty', 'salaries'));
    }

    public function storeSalaryPayment(Request $request)
    {
        $request->validate([
            'faculty_id' => 'required|exists:faculty,id',
            'base_salary' => 'required|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'pay_date' => 'required|date',
            'status' => 'required|in:paid,pending',
        ]);

        \App\Models\Salary::create([
            'faculty_id' => $request->faculty_id,
            'base_salary' => $request->base_salary,
            'bonuses' => $request->input('bonuses', 0),
            'deductions' => $request->input('deductions', 0),
            'pay_date' => $request->pay_date,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.payroll')->with('success', 'Salary record created successfully!');
    }

    // Hostels Manager
    public function hostels()
    {
        $hostels = \App\Models\Hostel::all();
        $students = Student::all();
        $allotments = \App\Models\HostelAllotment::with(['hostel', 'student'])->get();
        return view('admin.hostels', compact('hostels', 'students', 'allotments'));
    }

    public function storeHostel(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:boys,girls',
            'capacity' => 'required|integer|min:1',
            'address' => 'nullable|string',
        ]);

        \App\Models\Hostel::create($request->all());
        return redirect()->route('admin.hostels')->with('success', 'Hostel created successfully!');
    }

    public function allotRoom(Request $request)
    {
        $request->validate([
            'hostel_id' => 'required|exists:hostels,id',
            'room_no' => 'required|string|max:50',
            'student_id' => 'required|exists:students,id',
        ]);

        \App\Models\HostelAllotment::create($request->all());
        return redirect()->route('admin.hostels')->with('success', 'Hostel room allotted successfully!');
    }

    // Transports Manager
    public function transports()
    {
        $transports = \App\Models\Transport::all();
        $students = Student::all();
        $allotments = \App\Models\TransportAllotment::with(['transport', 'student'])->get();
        return view('admin.transports', compact('transports', 'students', 'allotments'));
    }

    public function storeTransport(Request $request)
    {
        $request->validate([
            'route_name' => 'required|string|max:255',
            'vehicle_no' => 'required|string|max:50',
            'driver_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        \App\Models\Transport::create($request->all());
        return redirect()->route('admin.transports')->with('success', 'Transport route created successfully!');
    }

    public function allotBus(Request $request)
    {
        $request->validate([
            'transport_id' => 'required|exists:transports,id',
            'student_id' => 'required|exists:students,id',
        ]);

        \App\Models\TransportAllotment::create($request->all());
        return redirect()->route('admin.transports')->with('success', 'Bus route allotted successfully!');
    }

    // Library Manager
    public function library()
    {
        $books = \App\Models\Book::all();
        $users = User::with(['student', 'faculty'])->get();
        $issues = \App\Models\BookIssue::with(['book', 'user'])->get();
        return view('admin.library', compact('books', 'users', 'issues'));
    }

    public function storeBook(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:50',
            'category' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
        ]);

        \App\Models\Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'category' => $request->category,
            'quantity' => $request->quantity,
            'available_quantity' => $request->quantity,
        ]);

        return redirect()->route('admin.library')->with('success', 'Book added to library catalog!');
    }

    public function issueBook(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'user_id' => 'required|exists:users,id',
            'return_due_date' => 'required|date|after:today',
        ]);

        $book = \App\Models\Book::findOrFail($request->book_id);
        if ($book->available_quantity <= 0) {
            return back()->with('error', 'Book copy is currently unavailable for issuing.');
        }

        try {
            DB::beginTransaction();
            \App\Models\BookIssue::create([
                'book_id' => $request->book_id,
                'user_id' => $request->user_id,
                'return_due_date' => $request->return_due_date,
            ]);

            $book->decrement('available_quantity');
            DB::commit();

            return redirect()->route('admin.library')->with('success', 'Book issued successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to issue book: ' . $e->getMessage());
        }
    }

    public function returnBook($id)
    {
        $issue = \App\Models\BookIssue::findOrFail($id);
        if ($issue->returned_at) {
            return back()->with('error', 'Book already returned.');
        }

        try {
            DB::beginTransaction();
            $issue->update([
                'returned_at' => now(),
            ]);

            $book = \App\Models\Book::findOrFail($issue->book_id);
            $book->increment('available_quantity');
            DB::commit();

            return redirect()->route('admin.library')->with('success', 'Book marked as returned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process return: ' . $e->getMessage());
        }
    }

    // Reports & Analytics
    public function reports()
    {
        $totalStudents = Student::count();
        $totalFaculty = Faculty::count();
        
        $paidFees = \App\Models\Fee::where('status', 'paid')->sum('amount');
        $unpaidFees = \App\Models\Fee::where('status', 'unpaid')->sum('amount');
        
        $totalBooks = \App\Models\Book::sum('quantity');
        $issuedBooks = \App\Models\BookIssue::whereNull('returned_at')->count();
        
        $leaveStats = [
            'pending' => \App\Models\LeaveApplication::where('status', 'pending')->count(),
            'approved' => \App\Models\LeaveApplication::where('status', 'approved')->count(),
            'rejected' => \App\Models\LeaveApplication::where('status', 'rejected')->count(),
        ];

        // Academic average grades
        $marksAverage = \App\Models\Mark::select('exam_type', DB::raw('AVG(marks_obtained / max_marks * 100) as average'))
            ->groupBy('exam_type')
            ->get();

        // Department-wise student-faculty analysis
        $departmentsAnalysis = \App\Models\Department::withCount(['students', 'faculty'])->get();

        return view('admin.reports', compact(
            'totalStudents', 'totalFaculty', 'paidFees', 'unpaidFees', 
            'totalBooks', 'issuedBooks', 'leaveStats', 'marksAverage',
            'departmentsAnalysis'
        ));
    }

    public function aiTimetable()
    {
        $uploads = TimetableUpload::with('uploader')
            ->orderBy('created_at', 'desc')
            ->get();
        $departments = Department::orderBy('name')->get();

        // Load current timetable for preview
        $currentTimetable = Timetable::with(['department', 'course', 'subject', 'faculty'])
            ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), start_time")
            ->get();

        // Detect existing conflicts
        $existingFacultyConflicts = [];
        $existingStudentConflicts = [];
        $grouped = $currentTimetable->groupBy('faculty_id');
        foreach ($grouped as $facultyId => $slots) {
            $slotArr = $slots->values()->all();
            for ($i = 0; $i < count($slotArr); $i++) {
                for ($j = $i + 1; $j < count($slotArr); $j++) {
                    $a = $slotArr[$i]; $b = $slotArr[$j];
                    if ($a->day_of_week === $b->day_of_week && $a->start_time < $b->end_time && $a->end_time > $b->start_time) {
                        $existingFacultyConflicts[] = ['faculty' => $a->faculty, 'slot_a' => $a, 'slot_b' => $b];
                    }
                }
            }
        }
        $courseGroups = $currentTimetable->groupBy(function ($t) { return $t->course_id . '-' . $t->semester; });
        foreach ($courseGroups as $key => $slots) {
            $slotArr = $slots->values()->all();
            for ($i = 0; $i < count($slotArr); $i++) {
                for ($j = $i + 1; $j < count($slotArr); $j++) {
                    $a = $slotArr[$i]; $b = $slotArr[$j];
                    if ($a->day_of_week === $b->day_of_week && $a->start_time < $b->end_time && $a->end_time > $b->start_time) {
                        $existingStudentConflicts[] = ['course' => $a->course, 'semester' => $a->semester, 'slot_a' => $a, 'slot_b' => $b];
                    }
                }
            }
        }

        // Assigned faculty in current timetable
        $assignedFacultyIds = $currentTimetable->pluck('faculty_id')->unique();
        $assignedFaculty = Faculty::with('department')->whereIn('id', $assignedFacultyIds)->get();

        // Affected students (students in courses/semesters that have timetable entries)
        $courseSemPairs = $currentTimetable->map(function($t) { return ['course_id' => $t->course_id, 'semester' => $t->semester]; })->unique();
        $affectedStudents = collect();
        foreach ($courseSemPairs as $pair) {
            $students = Student::with('course')
                ->where('course_id', $pair['course_id'])
                ->where('current_semester', $pair['semester'])
                ->get();
            $affectedStudents = $affectedStudents->merge($students);
        }
        $affectedStudents = $affectedStudents->unique('id');

        return view('admin.ai_timetable', compact(
            'uploads', 'departments', 'currentTimetable',
            'existingFacultyConflicts', 'existingStudentConflicts',
            'assignedFaculty', 'affectedStudents'
        ));
    }

    public function processAiTimetable(Request $request, GeminiAIService $geminiService)
    {
        $request->validate([
            'timetable_file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:20480',
            'department_id' => 'nullable|exists:departments,id',
            'semester' => 'nullable|integer|min:1|max:10',
        ]);

        $file = $request->file('timetable_file');
        $originalFilename = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        $destinationPath = public_path('uploads/timetables');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        
        $file->move($destinationPath, $fileName);
        $filePath = $destinationPath . '/' . $fileName;
        $relativeFilePath = 'uploads/timetables/' . $fileName;

        $upload = TimetableUpload::create([
            'file_path' => $relativeFilePath,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'uploaded_by' => Auth::id(),
            'status' => 'pending'
        ]);

        try {
            $upload->update(['status' => 'processing']);

            $aiResult = $geminiService->analyzeTimetable($filePath, $mimeType);

            if (!$aiResult || !$aiResult['success']) {
                throw new \Exception($aiResult['error'] ?? 'Gemini analysis failed or returned empty response.');
            }

            $data = $aiResult['data'];
            $slots = $data['slots'] ?? [];

            $slotsCreated = 0;
            $slotsSkipped = 0;
            $conflictsFound = 0;
            $unmatchedEntries = 0;

            $conflictReport = [];
            $unmatchedReport = [];
            $createdSlotsInfo = [];

            foreach ($slots as $slot) {
                $faculty = null;
                if (!empty($slot['faculty_name'])) {
                    $name = trim($slot['faculty_name']);
                    $cleanedName = preg_replace('/^(dr\.|prof\.|mr\.|ms\.|mrs\.)\s+/i', '', $name);
                    $parts = array_filter(explode(' ', $cleanedName));
                    if (count($parts) >= 2) {
                        $firstName = $parts[0];
                        $lastName = end($parts);
                        $faculty = Faculty::where('first_name', 'LIKE', "%{$firstName}%")
                            ->where('last_name', 'LIKE', "%{$lastName}%")
                            ->first();
                    }
                    if (!$faculty && count($parts) > 0) {
                        $searchName = $parts[0];
                        $faculty = Faculty::where('first_name', 'LIKE', "%{$searchName}%")
                            ->orWhere('last_name', 'LIKE', "%{$searchName}%")
                            ->first();
                    }
                }

                $subject = null;
                if (!empty($slot['subject_code'])) {
                    $subject = Subject::where('code', trim($slot['subject_code']))->first();
                }
                if (!$subject && !empty($slot['subject_name'])) {
                    $subject = Subject::where('name', 'LIKE', '%' . trim($slot['subject_name']) . '%')->first();
                }

                $course = null;
                if ($subject) {
                    $course = $subject->course;
                }
                if (!$course && !empty($slot['course_name'])) {
                    if ($request->filled('department_id')) {
                        $course = Course::where('department_id', $request->department_id)
                            ->where('name', 'LIKE', '%' . trim($slot['course_name']) . '%')
                            ->first();
                    } else {
                        $course = Course::where('name', 'LIKE', '%' . trim($slot['course_name']) . '%')->first();
                    }
                }

                // --- Automatic Dynamic Creation of Missing Entities ---
                
                // 1. Resolve / Create Course and Department
                if (!$course) {
                    $courseName = !empty($slot['course_name']) ? trim($slot['course_name']) : 'Computer Science';
                    
                    if ($request->filled('department_id')) {
                        $course = Course::where('department_id', $request->department_id)
                            ->where('name', 'LIKE', '%' . $courseName . '%')
                            ->first();
                        
                        if (!$course) {
                            $course = Course::create([
                                'name' => $courseName,
                                'department_id' => $request->department_id,
                                'duration_years' => 3
                            ]);
                        }
                    } else {
                        $course = Course::where('name', 'LIKE', '%' . $courseName . '%')->first();
                        
                        if (!$course) {
                            $deptName = !empty($slot['department']) ? trim($slot['department']) : 'Computer Science';
                            $dept = Department::where('name', 'LIKE', '%' . $deptName . '%')->first();
                            if (!$dept) {
                                $deptCode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $deptName), 0, 4));
                                if (empty($deptCode)) {
                                    $deptCode = 'CS';
                                }
                                $baseCode = $deptCode;
                                $counter = 1;
                                while (Department::where('code', $deptCode)->exists()) {
                                    $deptCode = $baseCode . $counter++;
                                }
                                $dept = Department::create([
                                    'name' => $deptName,
                                    'code' => $deptCode
                                ]);
                            }
                            
                            $course = Course::create([
                                'name' => $courseName,
                                'department_id' => $dept->id,
                                'duration_years' => 3
                            ]);
                        }
                    }
                }

                $departmentId = $request->input('department_id') ?? $course->department_id;
                $semester = $request->input('semester') ?? $slot['semester'] ?? $slot['semester_number'] ?? ($subject ? $subject->semester : 1);

                // 2. Resolve / Create Subject
                if (!$subject) {
                    $subjectCode = !empty($slot['subject_code']) ? trim($slot['subject_code']) : null;
                    $subjectName = !empty($slot['subject_name']) ? trim($slot['subject_name']) : 'General Subject';
                    
                    if ($subjectCode) {
                        $subject = Subject::where('code', $subjectCode)->first();
                    }
                    if (!$subject) {
                        $subject = Subject::where('name', 'LIKE', '%' . $subjectName . '%')->first();
                    }
                    
                    if (!$subject) {
                        if (!$subjectCode) {
                            $subjectCode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $subjectName), 0, 4)) . rand(100, 999);
                        }
                        $baseSubCode = $subjectCode;
                        $counter = 1;
                        while (Subject::where('code', $subjectCode)->exists()) {
                            $subjectCode = $baseSubCode . '_' . $counter++;
                        }
                        $subject = Subject::create([
                            'name' => $subjectName,
                            'code' => $subjectCode,
                            'course_id' => $course->id,
                            'semester' => $semester
                        ]);
                    }
                }

                // 3. Resolve / Create Faculty
                if (!$faculty) {
                    $facultyName = !empty($slot['faculty_name']) ? trim($slot['faculty_name']) : 'Temporary Faculty';
                    $cleanedName = preg_replace('/^(dr\.|prof\.|mr\.|ms\.|mrs\.)\s+/i', '', $facultyName);
                    $parts = array_filter(explode(' ', $cleanedName));
                    $firstName = $parts[0] ?? 'Temporary';
                    $lastName = end($parts);
                    if ($lastName === $firstName) {
                        $lastName = 'Faculty';
                    }
                    
                    $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName . rand(10, 99)));
                    while (User::where('username', $username)->exists()) {
                        $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName . rand(100, 999)));
                    }
                    
                    $role = Role::where('name', 'faculty')->first();
                    $roleId = $role ? $role->id : 2;
                    
                    $user = User::create([
                        'username' => $username,
                        'password' => Hash::make('faculty123'),
                        'email' => $username . '@college.edu',
                        'role_id' => $roleId
                    ]);
                    
                    $faculty = Faculty::create([
                        'user_id' => $user->id,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'department_id' => $course->department_id,
                        'phone' => '0000000000'
                    ]);
                }
                
                // Associate Subject with Faculty if not done
                if ($subject && !$subject->faculty_id) {
                    $subject->update(['faculty_id' => $faculty->id]);
                }
                
                // Robust Day of Week parsing
                $dayInput = strtolower(trim($slot['day_of_week'] ?? ''));
                $dayOfWeek = 'Monday'; // default fallback
                if (str_contains($dayInput, 'mon')) $dayOfWeek = 'Monday';
                elseif (str_contains($dayInput, 'tue')) $dayOfWeek = 'Tuesday';
                elseif (str_contains($dayInput, 'wed')) $dayOfWeek = 'Wednesday';
                elseif (str_contains($dayInput, 'thu')) $dayOfWeek = 'Thursday';
                elseif (str_contains($dayInput, 'fri')) $dayOfWeek = 'Friday';
                elseif (str_contains($dayInput, 'sat')) $dayOfWeek = 'Saturday';
                elseif (str_contains($dayInput, 'sun')) $dayOfWeek = 'Sunday';

                // Robust Start/End Time parsing
                $startTime = null;
                if (!empty($slot['start_time'])) {
                    $ts = strtotime(trim($slot['start_time']));
                    if ($ts !== false) {
                        $startTime = date('H:i:00', $ts);
                    }
                }
                $endTime = null;
                if (!empty($slot['end_time'])) {
                    $ts = strtotime(trim($slot['end_time']));
                    if ($ts !== false) {
                        $endTime = date('H:i:00', $ts);
                    }
                }
                
                if (!$startTime || !$endTime) {
                    $startTime = trim($slot['start_time'] ?? '09:00:00');
                    $endTime = trim($slot['end_time'] ?? '10:00:00');
                    if (strlen($startTime) == 5) $startTime .= ':00';
                    if (strlen($endTime) == 5) $endTime .= ':00';
                }

                $room = trim($slot['room'] ?? '');

                // Check for faculty conflict (same faculty, same day, overlapping time)
                $facultyConflict = Timetable::where('faculty_id', $faculty->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->where(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                    })->first();

                // Check for student group conflict (same course+semester, same day, overlapping time)
                $studentGroupConflict = Timetable::where('course_id', $course->id)
                    ->where('semester', $semester)
                    ->where('day_of_week', $dayOfWeek)
                    ->where(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                    })->first();

                // Check for room conflict
                $roomConflict = null;
                if (!empty($room)) {
                    $roomConflict = Timetable::where('room', $room)
                        ->where('day_of_week', $dayOfWeek)
                        ->where(function($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<', $endTime)
                              ->where('end_time', '>', $startTime);
                        })->first();
                }

                if ($facultyConflict || $roomConflict || $studentGroupConflict) {
                    $conflictsFound++;
                    $reasons = [];
                    if ($facultyConflict) {
                        $fName = $faculty->first_name . ' ' . $faculty->last_name;
                        $reasons[] = "Teacher conflict: {$fName} is already teaching " . ($facultyConflict->subject->name ?? 'a subject') . " ({$facultyConflict->start_time} - {$facultyConflict->end_time})";
                    }
                    if ($studentGroupConflict) {
                        $reasons[] = "Student group conflict: " . ($course->name ?? 'Course') . " Sem {$semester} already has " . ($studentGroupConflict->subject->name ?? 'a class') . " at this time";
                    }
                    if ($roomConflict) {
                        $reasons[] = "Room conflict: Room '{$room}' is booked for " . ($roomConflict->subject->name ?? 'a class') . " ({$roomConflict->start_time} - {$roomConflict->end_time})";
                    }
                    $conflictReport[] = [
                        'slot' => $slot,
                        'reason' => implode(' | ', $reasons),
                        'conflict_types' => [
                            'faculty' => $facultyConflict ? true : false,
                            'student_group' => $studentGroupConflict ? true : false,
                            'room' => $roomConflict ? true : false,
                        ]
                    ];
                    continue;
                }

                $duplicate = Timetable::where('department_id', $departmentId)
                    ->where('course_id', $course->id)
                    ->where('semester', $semester)
                    ->where('subject_id', $subject->id)
                    ->where('faculty_id', $faculty->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('start_time', $startTime)
                    ->where('end_time', $endTime)
                    ->where('room', $room)
                    ->exists();

                if ($duplicate) {
                    $slotsSkipped++;
                    continue;
                }

                $timetableEntry = Timetable::create([
                    'department_id' => $departmentId,
                    'course_id' => $course->id,
                    'semester' => $semester,
                    'subject_id' => $subject->id,
                    'faculty_id' => $faculty->id,
                    'day_of_week' => $dayOfWeek,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'room' => $room
                ]);

                $slotsCreated++;
                $createdSlotsInfo[] = [
                    'id' => $timetableEntry->id,
                    'subject' => $subject->name,
                    'faculty' => $faculty->first_name . ' ' . $faculty->last_name,
                    'day' => $dayOfWeek,
                    'time' => "{$startTime} - {$endTime}",
                    'room' => $room
                ];

                AppNotification::create([
                    'user_id' => $faculty->user_id,
                    'title' => 'New Timetable Schedule Allocated',
                    'message' => "You have been assigned to teach {$subject->name} on {$dayOfWeek} at {$startTime} - {$endTime} in {$room}.",
                    'type' => 'info',
                    'icon' => 'fas fa-calendar-alt',
                    'link' => route('faculty.my-attendance')
                ]);
            }

            $finalStatus = 'processed';
            if ($slotsCreated == 0 && count($slots) > 0) {
                $finalStatus = 'failed';
            } elseif ($conflictsFound > 0 || $unmatchedEntries > 0) {
                $finalStatus = 'partial';
            }

            $upload->update([
                'status' => $finalStatus,
                'slots_created' => $slotsCreated,
                'slots_skipped' => $slotsSkipped,
                'conflicts_found' => $conflictsFound,
                'unmatched_entries' => $unmatchedEntries,
                'ai_raw_response' => json_encode($data),
                'ai_source' => $aiResult['source'] ?? 'gemini',
                'processing_summary' => json_encode([
                    'conflicts' => $conflictReport,
                    'unmatched' => $unmatchedReport,
                    'created' => $createdSlotsInfo
                ])
            ]);

            AiAuditLog::create([
                'action_type' => 'timetable_process',
                'user_id' => Auth::id(),
                'target_table' => 'timetable_uploads',
                'target_id' => $upload->id,
                'details' => json_encode([
                    'slots_count' => count($slots),
                    'created' => $slotsCreated,
                    'skipped' => $slotsSkipped,
                    'conflicts' => $conflictsFound,
                    'unmatched' => $unmatchedEntries
                ]),
                'status' => $finalStatus == 'processed' ? 'success' : 'partial'
            ]);

            AppNotification::create([
                'user_id' => Auth::id(),
                'title' => 'Timetable OCR Processed',
                'message' => "Timetable '{$originalFilename}' has been processed. Created: {$slotsCreated}, Skipped: {$slotsSkipped}, Conflicts: {$conflictsFound}, Unmatched: {$unmatchedEntries}.",
                'type' => $finalStatus == 'processed' ? 'success' : 'warning',
                'icon' => 'fas fa-file-invoice',
                'link' => route('admin.ai-timetable')
            ]);

            // Gather assigned faculty and affected students for the response
            $allTimetable = Timetable::with(['subject', 'faculty', 'course'])->get();
            $assignedFacultyIds = $allTimetable->pluck('faculty_id')->unique();
            $assignedFacultyList = Faculty::with('department')->whereIn('id', $assignedFacultyIds)->get()->map(function($f) use ($allTimetable) {
                $slotsCount = $allTimetable->where('faculty_id', $f->id)->count();
                return [
                    'id' => $f->id,
                    'name' => $f->first_name . ' ' . $f->last_name,
                    'department' => $f->department->name ?? 'N/A',
                    'slots_count' => $slotsCount,
                ];
            })->values();

            $courseSemPairs = $allTimetable->map(function($t) { return ['course_id' => $t->course_id, 'semester' => $t->semester]; })->unique();
            $affectedStudentsList = collect();
            foreach ($courseSemPairs as $pair) {
                $students = Student::with('course')
                    ->where('course_id', $pair['course_id'])
                    ->where('current_semester', $pair['semester'])
                    ->get();
                $affectedStudentsList = $affectedStudentsList->merge($students);
            }
            $affectedStudentsList = $affectedStudentsList->unique('id')->map(function($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->first_name . ' ' . $s->last_name,
                    'enrollment_no' => $s->enrollment_no,
                    'course' => $s->course->name ?? 'N/A',
                    'semester' => $s->current_semester,
                ];
            })->values();

            // Count teacher and student group conflicts in the conflict report
            $teacherConflictCount = collect($conflictReport)->filter(function($c) { return ($c['conflict_types']['faculty'] ?? false); })->count();
            $studentConflictCount = collect($conflictReport)->filter(function($c) { return ($c['conflict_types']['student_group'] ?? false); })->count();

            return response()->json([
                'success' => true,
                'status' => $finalStatus,
                'message' => "Timetable parsed. Created: {$slotsCreated}, Skipped: {$slotsSkipped}, Conflicts: {$conflictsFound}, Unmatched: {$unmatchedEntries}.",
                'data' => [
                    'upload' => $upload,
                    'created' => $createdSlotsInfo,
                    'conflicts' => $conflictReport,
                    'unmatched' => $unmatchedReport,
                    'assigned_faculty' => $assignedFacultyList,
                    'affected_students' => $affectedStudentsList,
                    'teacher_conflict_count' => $teacherConflictCount,
                    'student_conflict_count' => $studentConflictCount,
                ]
            ]);

        } catch (\Exception $e) {
            $upload->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            AiAuditLog::create([
                'action_type' => 'timetable_process',
                'user_id' => Auth::id(),
                'target_table' => 'timetable_uploads',
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

    // Admin Exams Master Section
    public function exams(Request $request)
    {
        $departments = Department::orderBy('name')->get();
        $subjects = Subject::with('course.department')->get();
        
        $query = Exam::with(['subject.course.department', 'faculty.user', 'questions']);
        
        if ($request->filled('department_id')) {
            $query->whereHas('subject.course', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        
        if ($request->filled('semester')) {
            $query->whereHas('subject', function($q) use ($request) {
                $q->where('semester', $request->semester);
            });
        }
        
        $exams = $query->orderBy('id', 'desc')->get();
        
        return view('admin.exams.index', compact('exams', 'subjects', 'departments'));
    }

    public function storeExam(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'duration_minutes' => 'required|integer|min:5|max:180',
        ]);

        $subject = Subject::findOrFail($request->input('subject_id'));

        Exam::create([
            'title' => trim($request->input('title')),
            'subject_id' => $request->input('subject_id'),
            'duration_minutes' => $request->input('duration_minutes'),
            'faculty_id' => $subject->faculty_id,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.exams')->with('success', 'Exam created successfully!');
    }

    public function toggleExamStatus(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,active,completed',
        ]);

        $exam->update(['status' => $request->input('status')]);
        return redirect()->route('admin.exams')->with('success', 'Exam status updated to ' . $request->input('status') . '!');
    }

    public function deleteExam($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();

        return redirect()->route('admin.exams')->with('success', 'Exam deleted successfully!');
    }

    public function manageExam($id)
    {
        $exam = Exam::with('questions')->findOrFail($id);

        return view('admin.exams.questions', compact('exam'));
    }

    public function addQuestion(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

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

        return redirect()->route('admin.exams.manage', $exam->id)->with('success', 'Question added successfully!');
    }

    public function deleteQuestion($id)
    {
        $question = ExamQuestion::findOrFail($id);
        $examId = $question->exam_id;
        $question->delete();
        return redirect()->route('admin.exams.manage', $examId)->with('success', 'Question deleted successfully!');
    }

    public function updateQuestion(Request $request, $id)
    {
        $question = ExamQuestion::findOrFail($id);

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

        return redirect()->route('admin.exams.manage', $question->exam_id)->with('success', 'Question updated successfully!');
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

        return response()->json([
            'success' => false,
            'message' => 'Failed to parse questions using AI. Ensure GEMINI_API_KEY is configured.'
        ], 500);
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

    public function viewAttempts($id)
    {
        $exam = Exam::findOrFail($id);
        $attempts = ExamAttempt::with('student')->where('exam_id', $exam->id)->get();

        return view('admin.exams.attempts', compact('exam', 'attempts'));
    }

    public function examResults(Request $request)
    {
        $departments = Department::orderBy('name')->get();
        $exams = Exam::orderBy('title')->get();
        
        $query = ExamAttempt::with(['student.course.department', 'student.user', 'exam.subject']);
        
        if ($request->filled('department_id')) {
            $query->whereHas('student.course', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        
        if ($request->filled('semester')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('current_semester', $request->semester);
            });
        }
        
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        
        $attempts = $query->orderBy('id', 'desc')->get();
        
        return view('admin.exams.results', compact('attempts', 'departments', 'exams'));
    }

    public function reviewAttempt($attempt_id)
    {
        $attempt = ExamAttempt::with(['student.user', 'exam.subject'])->findOrFail($attempt_id);
        $exam = $attempt->exam;
        $questions = ExamQuestion::where('exam_id', $exam->id)->get();
        $answers = ExamAnswer::where('attempt_id', $attempt->id)->get()->keyBy('question_id');

        return view('admin.exams.review_modal', compact('attempt', 'exam', 'questions', 'answers'));
    }
}
