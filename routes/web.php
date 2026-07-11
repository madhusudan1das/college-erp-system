<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\StudentController;

// Base Redirect Route
Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role->name;
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'faculty') {
            return redirect()->route('faculty.dashboard');
        } elseif ($role === 'student') {
            return redirect()->route('student.dashboard');
        }
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Guest Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('password.forgot');
Route::post('/forgot-password', [AuthController::class, 'forgotPasswordVerify'])->name('password.forgot.verify');
Route::get('/reset-password', [AuthController::class, 'resetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset.post');

// Authenticated User Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [AuthController::class, 'changePasswordForm'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.change.update');
    Route::post('/notifications/{id}/read', [AuthController::class, 'markNotificationRead'])->name('notifications.read');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Students CRUD
    Route::get('/students', [AdminController::class, 'students'])->name('students');
    Route::get('/students/create', [AdminController::class, 'addStudentForm'])->name('students.create');
    Route::post('/students', [AdminController::class, 'addStudent'])->name('students.store');
    Route::get('/students/{id}/edit', [AdminController::class, 'editStudentForm'])->name('students.edit');
    Route::put('/students/{id}', [AdminController::class, 'editStudent'])->name('students.update');
    Route::delete('/students/{id}', [AdminController::class, 'deleteStudent'])->name('students.delete');

    // Faculty CRUD
    Route::get('/faculty', [AdminController::class, 'faculty'])->name('faculty');
    Route::get('/faculty/create', [AdminController::class, 'addFacultyForm'])->name('faculty.create');
    Route::post('/faculty', [AdminController::class, 'addFaculty'])->name('faculty.store');
    Route::get('/faculty/{id}/edit', [AdminController::class, 'editFacultyForm'])->name('faculty.edit');
    Route::put('/faculty/{id}', [AdminController::class, 'editFaculty'])->name('faculty.update');
    Route::delete('/faculty/{id}', [AdminController::class, 'deleteFaculty'])->name('faculty.delete');

    // Courses CRUD
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::post('/courses', [AdminController::class, 'storeCourse'])->name('courses.store');
    Route::delete('/courses/{id}', [AdminController::class, 'deleteCourse'])->name('courses.delete');

    // Departments CRUD
    Route::get('/departments', [AdminController::class, 'departments'])->name('departments');
    Route::post('/departments', [AdminController::class, 'storeDepartment'])->name('departments.store');
    Route::delete('/departments/{id}', [AdminController::class, 'deleteDepartment'])->name('departments.delete');

    // Subjects CRUD
    Route::get('/subjects', [AdminController::class, 'subjects'])->name('subjects');
    Route::post('/subjects', [AdminController::class, 'storeSubject'])->name('subjects.store');
    Route::delete('/subjects/{id}', [AdminController::class, 'deleteSubject'])->name('subjects.delete');

    // Timetable CRUD
    Route::get('/timetable', [AdminController::class, 'timetable'])->name('timetable');
    Route::post('/timetable', [AdminController::class, 'storeTimetable'])->name('timetable.store');
    Route::put('/timetable/{id}', [AdminController::class, 'updateTimetable'])->name('timetable.update');
    Route::delete('/timetable/{id}', [AdminController::class, 'deleteTimetable'])->name('timetable.delete');

    // Notices CRUD
    Route::get('/notices', [AdminController::class, 'notices'])->name('notices');
    Route::post('/notices', [AdminController::class, 'storeNotice'])->name('notices.store');
    Route::delete('/notices/{id}', [AdminController::class, 'deleteNotice'])->name('notices.delete');

    // Faculty Attendance
    Route::get('/faculty-attendance', [AdminController::class, 'facultyAttendance'])->name('faculty-attendance');
    Route::post('/faculty-attendance', [AdminController::class, 'storeFacultyAttendance'])->name('faculty-attendance.store');

    // Department-Wise Directory
    Route::get('/department-wise', [AdminController::class, 'departmentWise'])->name('department-wise');

    // Leave Requests
    Route::get('/leaves', [AdminController::class, 'leaves'])->name('leaves');
    Route::post('/leaves/{id}/approve', [AdminController::class, 'approveLeave'])->name('leaves.approve');
    Route::post('/leaves/{id}/reject', [AdminController::class, 'rejectLeave'])->name('leaves.reject');

    // Grievances
    Route::get('/complaints', [AdminController::class, 'complaints'])->name('complaints');
    Route::post('/complaints/{id}/resolve', [AdminController::class, 'resolveComplaint'])->name('complaints.resolve');

    // Fees Manager
    Route::get('/fees', [AdminController::class, 'fees'])->name('fees');
    Route::post('/fees', [AdminController::class, 'storeFeeDues'])->name('fees.store');

    // Payroll Manager
    Route::get('/payroll', [AdminController::class, 'payroll'])->name('payroll');
    Route::post('/payroll', [AdminController::class, 'storeSalaryPayment'])->name('payroll.store');

    // Hostels Manager
    Route::get('/hostels', [AdminController::class, 'hostels'])->name('hostels');
    Route::post('/hostels', [AdminController::class, 'storeHostel'])->name('hostels.store');
    Route::post('/hostels/allot', [AdminController::class, 'allotRoom'])->name('hostels.allot');

    // Transports Manager
    Route::get('/transports', [AdminController::class, 'transports'])->name('transports');
    Route::post('/transports', [AdminController::class, 'storeTransport'])->name('transports.store');
    Route::post('/transports/allot', [AdminController::class, 'allotBus'])->name('transports.allot');

    // Library Manager
    Route::get('/library', [AdminController::class, 'library'])->name('library');
    Route::post('/library', [AdminController::class, 'storeBook'])->name('library.store');
    Route::post('/library/issue', [AdminController::class, 'issueBook'])->name('library.issue');
    Route::post('/library/return/{id}', [AdminController::class, 'returnBook'])->name('library.return');

    // Reports & Analytics
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');

    // AI Timetable
    Route::get('/ai-timetable', [AdminController::class, 'aiTimetable'])->name('ai-timetable');
    Route::post('/ai-timetable/process', [AdminController::class, 'processAiTimetable'])->name('ai-timetable.process');

    // Admin Exams Master Section
    Route::get('/exams', [AdminController::class, 'exams'])->name('exams');
    Route::post('/exams', [AdminController::class, 'storeExam'])->name('exams.store');
    Route::post('/exams/{id}/toggle', [AdminController::class, 'toggleExamStatus'])->name('exams.toggle');
    Route::delete('/exams/{id}', [AdminController::class, 'deleteExam'])->name('exams.delete');
    Route::get('/exams/{id}/manage', [AdminController::class, 'manageExam'])->name('exams.manage');
    Route::post('/exams/{id}/questions', [AdminController::class, 'addQuestion'])->name('exams.questions.store');
    Route::post('/exams/{id}/parse-questions', [AdminController::class, 'parseQuestions'])->name('exams.questions.parse');
    Route::post('/exams/{id}/generate-questions', [AdminController::class, 'autoGenerateQuestions'])->name('exams.questions.generate');
    Route::post('/exams/{id}/import-questions', [AdminController::class, 'importQuestions'])->name('exams.questions.import');
    Route::delete('/exams/questions/{id}', [AdminController::class, 'deleteQuestion'])->name('exams.questions.delete');
    Route::put('/exams/questions/{id}', [AdminController::class, 'updateQuestion'])->name('exams.questions.update');
    Route::get('/exams/{id}/attempts', [AdminController::class, 'viewAttempts'])->name('exams.attempts');
    Route::get('/exams/results', [AdminController::class, 'examResults'])->name('results');
    Route::get('/exams/attempts/{attempt_id}/review', [AdminController::class, 'reviewAttempt'])->name('exams.attempts.review');
});

// Faculty Routes
Route::middleware(['auth', 'role:faculty'])->prefix('faculty')->name('faculty.')->group(function () {
    Route::get('/dashboard', [FacultyController::class, 'dashboard'])->name('dashboard');
    Route::get('/my-attendance', [FacultyController::class, 'myAttendance'])->name('my-attendance');
    Route::get('/timetable', [FacultyController::class, 'timetable'])->name('timetable');
    
    // Student Attendance
    Route::get('/attendance', [FacultyController::class, 'attendance'])->name('attendance');
    Route::post('/attendance', [FacultyController::class, 'storeAttendance'])->name('attendance.store');

    // Marks
    Route::get('/marks', [FacultyController::class, 'marks'])->name('marks');
    Route::post('/marks', [FacultyController::class, 'storeMarks'])->name('marks.store');

    // Assignments
    Route::get('/assignments', [FacultyController::class, 'assignments'])->name('assignments');
    Route::post('/assignments', [FacultyController::class, 'storeAssignment'])->name('assignments.store');
    Route::delete('/assignments/{id}', [FacultyController::class, 'deleteAssignment'])->name('assignments.delete');
    Route::get('/assignments/{id}/submissions', [FacultyController::class, 'viewAssignmentSubmissions'])->name('assignments.submissions');
    Route::post('/submissions/{id}/grade', [FacultyController::class, 'gradeSubmission'])->name('assignments.grade');

    // Exams
    Route::get('/exams', [FacultyController::class, 'exams'])->name('exams');
    Route::post('/exams', [FacultyController::class, 'storeExam'])->name('exams.store');
    Route::post('/exams/{id}/toggle', [FacultyController::class, 'toggleExamStatus'])->name('exams.toggle');
    Route::delete('/exams/{id}', [FacultyController::class, 'deleteExam'])->name('exams.delete');
    Route::get('/exams/{id}/manage', [FacultyController::class, 'manageExam'])->name('exams.manage');
    Route::post('/exams/{id}/questions', [FacultyController::class, 'addQuestion'])->name('exams.questions.store');
    Route::post('/exams/{id}/parse-questions', [FacultyController::class, 'parseQuestions'])->name('exams.questions.parse');
    Route::post('/exams/{id}/generate-questions', [FacultyController::class, 'autoGenerateQuestions'])->name('exams.questions.generate');
    Route::post('/exams/{id}/import-questions', [FacultyController::class, 'importQuestions'])->name('exams.questions.import');
    Route::delete('/exams/questions/{id}', [FacultyController::class, 'deleteQuestion'])->name('exams.questions.delete');
    Route::put('/exams/questions/{id}', [FacultyController::class, 'updateQuestion'])->name('exams.questions.update');
    Route::get('/exams/{id}/attempts', [FacultyController::class, 'viewAttempts'])->name('exams.attempts');

    // Notices
    Route::get('/notices', [FacultyController::class, 'notices'])->name('notices');

    // Study Materials
    Route::get('/materials', [FacultyController::class, 'materials'])->name('materials');
    Route::post('/materials', [FacultyController::class, 'storeMaterial'])->name('materials.store');
    Route::delete('/materials/{id}', [FacultyController::class, 'deleteMaterial'])->name('materials.delete');

    // Leave application
    Route::get('/leave', [FacultyController::class, 'leave'])->name('leave');
    Route::post('/leave', [FacultyController::class, 'storeLeave'])->name('leave.store');

    // Mentoring
    Route::get('/mentoring', [FacultyController::class, 'mentoring'])->name('mentoring');
    Route::post('/mentoring', [FacultyController::class, 'storeMentoring'])->name('mentoring.store');

    // Research
    Route::get('/research', [FacultyController::class, 'research'])->name('research');
    Route::post('/research', [FacultyController::class, 'storeResearch'])->name('research.store');

    // Forum
    Route::get('/forum', [FacultyController::class, 'forum'])->name('forum');
    Route::post('/forum/topic', [FacultyController::class, 'storeForumTopic'])->name('forum.topic.store');
    Route::post('/forum/reply', [FacultyController::class, 'storeForumReply'])->name('forum.reply.store');

    // Chat
    Route::get('/chat', [FacultyController::class, 'chat'])->name('chat');
    Route::post('/chat/send', [FacultyController::class, 'sendMessage'])->name('chat.send');

    // Answer Sheets
    Route::get('/answer-sheets', [FacultyController::class, 'answerSheets'])->name('answer-sheets');
    Route::post('/answer-sheets/upload', [FacultyController::class, 'uploadAnswerSheet'])->name('answer-sheets.upload');
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/attendance', [StudentController::class, 'attendance'])->name('attendance');
    Route::get('/results', [StudentController::class, 'results'])->name('results');
    
    // Assignments
    Route::get('/assignments', [StudentController::class, 'assignments'])->name('assignments');
    Route::post('/assignments/{id}/submit', [StudentController::class, 'submitAssignment'])->name('assignments.submit');

    // Timetable
    Route::get('/timetable', [StudentController::class, 'timetable'])->name('timetable');

    // Exams
    Route::get('/exams', [StudentController::class, 'exams'])->name('exams');
    Route::get('/exams/{id}/take', [StudentController::class, 'takeExam'])->name('exams.take');
    Route::post('/exams/{id}/submit', [StudentController::class, 'submitExam'])->name('exams.submit');
    Route::get('/exams/{id}/result', [StudentController::class, 'examResult'])->name('exams.result');

    // Study Materials
    Route::get('/materials', [StudentController::class, 'materials'])->name('materials');

    // Leave application
    Route::get('/leave', [StudentController::class, 'leave'])->name('leave');
    Route::post('/leave', [StudentController::class, 'storeLeave'])->name('leave.store');

    // Complaints
    Route::get('/complaints', [StudentController::class, 'complaints'])->name('complaints');
    Route::post('/complaints', [StudentController::class, 'storeComplaint'])->name('complaints.store');

    // Fees Portal
    Route::get('/fees', [StudentController::class, 'fees'])->name('fees');
    Route::post('/fees/{id}/initiate', [StudentController::class, 'initiatePayment'])->name('fees.initiate');
    Route::post('/fees/verify', [StudentController::class, 'verifyPayment'])->name('fees.verify');
    Route::get('/fees/{id}/receipt', [StudentController::class, 'viewReceipt'])->name('fees.receipt');

    // Campus Services
    Route::get('/services', [StudentController::class, 'services'])->name('services');

    // Forum
    Route::get('/forum', [StudentController::class, 'forum'])->name('forum');
    Route::post('/forum/topic', [StudentController::class, 'storeForumTopic'])->name('forum.topic.store');
    Route::post('/forum/reply', [StudentController::class, 'storeForumReply'])->name('forum.reply.store');

    // Chat
    Route::get('/chat', [StudentController::class, 'chat'])->name('chat');
    Route::post('/chat/send', [StudentController::class, 'sendMessage'])->name('chat.send');
});
