<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Smart College Assistant')</title>
    <meta name="description" content="Smart College Assistant - Comprehensive academic management portal.">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- Premium Stylesheet -->
    <link href="{{ asset('css/premium.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            color: #333;
            overflow-x: hidden;
        }
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            color: #fff;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            min-height: 100vh;
            position: relative;
            z-index: 100;
        }
        #sidebar.active {
            margin-left: -260px;
        }
        #sidebar .sidebar-header {
            padding: 20px;
        }
        #sidebar ul.components {
            padding: 12px 0;
        }
        #sidebar ul li a {
            padding: 15px 20px;
            font-size: 1.1em;
            display: block;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        #sidebar ul li a:hover, #sidebar ul li.active > a {
            color: #fff;
            background: #34495e;
            border-left: 4px solid #3498db;
        }
        #sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        #content {
            width: 100%;
            padding: 0;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            min-height: 100vh;
            background: #f0f2f5;
        }
        .top-navbar {
            background: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 90;
            animation: fadeInDown 0.4s ease forwards;
        }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #f0f0f0;
            font-weight: 600;
            border-radius: 16px 16px 0 0 !important;
            padding: 16px 20px;
        }
        .stat-card {
            color: #fff;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-card h3 {
            font-size: 2rem;
            margin: 0;
            font-weight: 700;
        }
        .stat-card .icon {
            font-size: 3rem;
            opacity: 0.5;
        }
        .bg-primary-grad { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .bg-success-grad { background: linear-gradient(135deg, #2af598 0%, #009efd 100%); }
        .bg-warning-grad { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
        .bg-danger-grad { background: linear-gradient(135deg, #ff0844 0%, #ffb199 100%); }

        /* Sidebar collapse button */
        .sidebar-toggle-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f2f5 !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            box-shadow: none !important;
            transition: all 0.3s ease;
        }

        .sidebar-toggle-btn:hover {
            background: #e2e8f0 !important;
            transform: none !important;
        }

        /* User dropdown */
        .user-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s;
        }

        .user-dropdown-btn:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.85rem;
        }

        /* Notification bell */
        .notification-bell {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            transition: all 0.3s;
            position: relative;
            text-decoration: none;
        }

        .notification-bell:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: #fff;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.65rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border: 2px solid #fff;
            animation: scaleIn 0.3s ease forwards;
        }

        /* Content area */
        .content-body {
            padding: 24px;
        }

        /* Alert styling */
        .alert {
            border-radius: 12px;
            border: none;
            animation: fadeInDown 0.4s ease forwards;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(5,150,105,0.05));
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239,68,68,0.1), rgba(220,38,38,0.05));
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        /* Role badge */
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .role-badge-admin { background: linear-gradient(135deg, #fecaca, #fee2e2); color: #991b1b; }
        .role-badge-faculty { background: linear-gradient(135deg, #c7d2fe, #e0e7ff); color: #3730a3; }
        .role-badge-student { background: linear-gradient(135deg, #a7f3d0, #d1fae5); color: #065f46; }

        /* Dropdown */
        .dropdown-menu {
            animation: fadeInUp 0.3s ease forwards;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            border-radius: 12px;
            padding: 8px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background: #f1f5f9;
        }

        /* Footer gradient line */
        .footer-gradient {
            height: 3px;
            background: linear-gradient(90deg, #4f46e5, #06b6d4, #8b5cf6, #4f46e5);
            background-size: 300% 100%;
            animation: gradientBg 4s ease infinite;
            margin-top: auto;
        }
    </style>
    @yield('styles')
</head>
<body>
    @php
        $unreadNotifications = [];
        $unreadCount = 0;
        if (Auth::check()) {
            $unreadNotifications = \App\Models\AppNotification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            $unreadCount = \App\Models\AppNotification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();
        }
    @endphp

    <!-- Page Loading Bar -->
    <div class="page-loader" id="pageLoader"></div>

    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header d-flex flex-column align-items-center justify-content-center py-4 text-center">
                <div class="mb-2" style="animation: scaleIn 0.5s ease 0.2s both;">
                    <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #fff; box-shadow: 0 4px 15px rgba(99,102,241,0.3);">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
                <h4 class="mb-1 fw-bold text-white" style="font-size: 1.15rem; letter-spacing: -0.3px;">Smart College</h4>
                @if (Auth::user()->role->name === 'admin')
                    <span class="role-badge role-badge-admin mt-1"><i class="fas fa-user-shield"></i> Admin Panel</span>
                @elseif (Auth::user()->role->name === 'faculty')
                    <span class="role-badge role-badge-faculty mt-1"><i class="fas fa-chalkboard-teacher"></i> Faculty Panel</span>
                @elseif (Auth::user()->role->name === 'student')
                    <span class="role-badge role-badge-student mt-1"><i class="fas fa-user-graduate"></i> Student Portal</span>
                @endif
            </div>
            <ul class="list-unstyled components">
                @if (Auth::user()->role->name === 'admin')
                    <li class="{{ Route::is('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li class="{{ Route::is('admin.students*') ? 'active' : '' }}">
                        <a href="{{ route('admin.students') }}"><i class="fas fa-user-graduate"></i> Students</a>
                    </li>
                    <li class="{{ Route::is('admin.faculty*') ? 'active' : '' }}">
                        <a href="{{ route('admin.faculty') }}"><i class="fas fa-chalkboard-teacher"></i> Faculty</a>
                    </li>
                    <li class="{{ Route::is('admin.courses*') ? 'active' : '' }}">
                        <a href="{{ route('admin.courses') }}"><i class="fas fa-book"></i> Courses</a>
                    </li>
                    <li class="{{ Route::is('admin.departments*') ? 'active' : '' }}">
                        <a href="{{ route('admin.departments') }}"><i class="fas fa-building"></i> Departments</a>
                    </li>
                    <li class="{{ Route::is('admin.subjects*') ? 'active' : '' }}">
                        <a href="{{ route('admin.subjects') }}"><i class="fas fa-book-open"></i> Subjects</a>
                    </li>
                    <li class="{{ Route::is('admin.timetable*') ? 'active' : '' }}">
                        <a href="{{ route('admin.timetable') }}"><i class="fas fa-calendar-alt"></i> Timetable</a>
                    </li>
                    <li class="{{ Route::is('admin.ai-timetable*') ? 'active' : '' }}">
                        <a href="{{ route('admin.ai-timetable') }}"><i class="fas fa-robot"></i> AI Timetable</a>
                    </li>
                    <li class="{{ Route::is('admin.notices*') ? 'active' : '' }}">
                        <a href="{{ route('admin.notices') }}"><i class="fas fa-bullhorn"></i> Notices</a>
                    </li>
                    <li class="{{ Route::is('admin.exams*') ? 'active' : '' }}">
                        <a href="{{ route('admin.exams') }}"><i class="fas fa-laptop-code"></i> Manage Exams</a>
                    </li>
                    <li class="{{ Route::is('admin.results*') ? 'active' : '' }}">
                        <a href="{{ route('admin.results') }}"><i class="fas fa-poll"></i> Exam Results</a>
                    </li>
                    <li class="{{ Route::is('admin.faculty-attendance*') ? 'active' : '' }}">
                        <a href="{{ route('admin.faculty-attendance') }}"><i class="fas fa-user-check"></i> Faculty Attendance</a>
                    </li>
                    <li class="{{ Route::is('admin.department-wise*') ? 'active' : '' }}">
                        <a href="{{ route('admin.department-wise') }}"><i class="fas fa-users"></i> Department Directory</a>
                    </li>
                    <li class="{{ Route::is('admin.leaves*') ? 'active' : '' }}">
                        <a href="{{ route('admin.leaves') }}"><i class="fas fa-plane-departure"></i> Leave Requests</a>
                    </li>
                    <li class="{{ Route::is('admin.complaints*') ? 'active' : '' }}">
                        <a href="{{ route('admin.complaints') }}"><i class="fas fa-exclamation-circle"></i> Grievances</a>
                    </li>
                    <li class="{{ Route::is('admin.fees*') ? 'active' : '' }}">
                        <a href="{{ route('admin.fees') }}"><i class="fas fa-money-bill-wave"></i> Fee Management</a>
                    </li>
                    <li class="{{ Route::is('admin.payroll*') ? 'active' : '' }}">
                        <a href="{{ route('admin.payroll') }}"><i class="fas fa-wallet"></i> Payroll (Salaries)</a>
                    </li>
                    <li class="{{ Route::is('admin.hostels*') ? 'active' : '' }}">
                        <a href="{{ route('admin.hostels') }}"><i class="fas fa-hotel"></i> Hostel Manager</a>
                    </li>
                    <li class="{{ Route::is('admin.transports*') ? 'active' : '' }}">
                        <a href="{{ route('admin.transports') }}"><i class="fas fa-bus"></i> Transport Manager</a>
                    </li>
                    <li class="{{ Route::is('admin.library*') ? 'active' : '' }}">
                        <a href="{{ route('admin.library') }}"><i class="fas fa-book-reader"></i> Library Manager</a>
                    </li>
                    <li class="{{ Route::is('admin.reports*') ? 'active' : '' }}">
                        <a href="{{ route('admin.reports') }}"><i class="fas fa-chart-bar"></i> Reports & Analytics</a>
                    </li>
                @elseif (Auth::user()->role->name === 'faculty')
                    <li class="{{ Route::is('faculty.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('faculty.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li class="{{ Route::is('faculty.my-attendance') ? 'active' : '' }}">
                        <a href="{{ route('faculty.my-attendance') }}"><i class="fas fa-user-check"></i> My Attendance</a>
                    </li>
                    <li class="{{ Route::is('faculty.timetable') ? 'active' : '' }}">
                        <a href="{{ route('faculty.timetable') }}"><i class="fas fa-calendar-alt"></i> My Timetable</a>
                    </li>
                    <li class="{{ Route::is('faculty.attendance') ? 'active' : '' }}">
                        <a href="{{ route('faculty.attendance') }}"><i class="fas fa-clipboard-list"></i> Student Attendance</a>
                    </li>
                    <li class="{{ Route::is('faculty.marks') ? 'active' : '' }}">
                        <a href="{{ route('faculty.marks') }}"><i class="fas fa-poll"></i> Marks</a>
                    </li>
                    <li class="{{ Route::is('faculty.assignments*') ? 'active' : '' }}">
                        <a href="{{ route('faculty.assignments') }}"><i class="fas fa-tasks"></i> Assignments</a>
                    </li>
                    <li class="{{ Route::is('faculty.exams*') ? 'active' : '' }}">
                        <a href="{{ route('faculty.exams') }}"><i class="fas fa-laptop-code"></i> Exams</a>
                    </li>
                    <li class="{{ Route::is('faculty.notices') ? 'active' : '' }}">
                        <a href="{{ route('faculty.notices') }}"><i class="fas fa-bullhorn"></i> Notices</a>
                    </li>
                    <li class="{{ Route::is('faculty.materials*') ? 'active' : '' }}">
                        <a href="{{ route('faculty.materials') }}"><i class="fas fa-folder-open"></i> Study Materials</a>
                    </li>
                    <li class="{{ Route::is('faculty.answer-sheets*') ? 'active' : '' }}">
                        <a href="{{ route('faculty.answer-sheets') }}"><i class="fas fa-file-alt"></i> Answer Sheets</a>
                    </li>
                    <li class="{{ Route::is('faculty.leave*') ? 'active' : '' }}">
                        <a href="{{ route('faculty.leave') }}"><i class="fas fa-plane-departure"></i> Apply Leave</a>
                    </li>
                    <li class="{{ Route::is('faculty.mentoring*') ? 'active' : '' }}">
                        <a href="{{ route('faculty.mentoring') }}"><i class="fas fa-users-cog"></i> Mentoring</a>
                    </li>
                    <li class="{{ Route::is('faculty.research*') ? 'active' : '' }}">
                        <a href="{{ route('faculty.research') }}"><i class="fas fa-graduation-cap"></i> Research Records</a>
                    </li>
                    <li class="{{ Route::is('faculty.forum*') ? 'active' : '' }}">
                        <a href="{{ route('faculty.forum') }}"><i class="fas fa-comments"></i> Discussion Forum</a>
                    </li>
                    <li class="{{ Route::is('faculty.chat*') ? 'active' : '' }}">
                        <a href="{{ route('faculty.chat') }}"><i class="fas fa-paper-plane"></i> Message Students</a>
                    </li>
                @elseif (Auth::user()->role->name === 'student')
                    <li class="{{ Route::is('student.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('student.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li class="{{ Route::is('student.attendance') ? 'active' : '' }}">
                        <a href="{{ route('student.attendance') }}"><i class="fas fa-clipboard-list"></i> Attendance</a>
                    </li>
                    <li class="{{ Route::is('student.results') ? 'active' : '' }}">
                        <a href="{{ route('student.results') }}"><i class="fas fa-poll"></i> Results</a>
                    </li>
                    <li class="{{ Route::is('student.assignments') ? 'active' : '' }}">
                        <a href="{{ route('student.assignments') }}"><i class="fas fa-tasks"></i> Assignments</a>
                    </li>
                    <li class="{{ Route::is('student.exams*') ? 'active' : '' }}">
                        <a href="{{ route('student.exams') }}"><i class="fas fa-laptop-code"></i> Exams</a>
                    </li>
                    <li class="{{ Route::is('student.timetable') ? 'active' : '' }}">
                        <a href="{{ route('student.timetable') }}"><i class="fas fa-calendar-alt"></i> Timetable</a>
                    </li>
                    <li class="{{ Route::is('student.materials*') ? 'active' : '' }}">
                        <a href="{{ route('student.materials') }}"><i class="fas fa-folder-open"></i> Study Materials</a>
                    </li>
                    <li class="{{ Route::is('student.leave*') ? 'active' : '' }}">
                        <a href="{{ route('student.leave') }}"><i class="fas fa-plane-departure"></i> Apply Leave</a>
                    </li>
                    <li class="{{ Route::is('student.complaints*') ? 'active' : '' }}">
                        <a href="{{ route('student.complaints') }}"><i class="fas fa-exclamation-circle"></i> Complaints</a>
                    </li>
                    <li class="{{ Route::is('student.fees*') ? 'active' : '' }}">
                        <a href="{{ route('student.fees') }}"><i class="fas fa-money-bill-wave"></i> Fees Portal</a>
                    </li>
                    <li class="{{ Route::is('student.services*') ? 'active' : '' }}">
                        <a href="{{ route('student.services') }}"><i class="fas fa-concierge-bell"></i> Campus Services</a>
                    </li>
                    <li class="{{ Route::is('student.forum*') ? 'active' : '' }}">
                        <a href="{{ route('student.forum') }}"><i class="fas fa-comments"></i> Discussion Forum</a>
                    </li>
                    <li class="{{ Route::is('student.chat*') ? 'active' : '' }}">
                        <a href="{{ route('student.chat') }}"><i class="fas fa-paper-plane"></i> Message Faculty</a>
                    </li>
                @endif
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="top-navbar">
                <div class="d-flex align-items-center justify-content-between">
                    <button type="button" id="sidebarCollapse" class="sidebar-toggle-btn btn">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="d-flex align-items-center gap-3">
                        @if (Auth::user()->role->name === 'admin')
                            <span class="role-badge role-badge-admin"><i class="fas fa-user-shield"></i> Admin</span>
                        @elseif (Auth::user()->role->name === 'faculty')
                            <span class="role-badge role-badge-faculty"><i class="fas fa-chalkboard-teacher"></i> Faculty</span>
                        @elseif (Auth::user()->role->name === 'student')
                            <span class="role-badge role-badge-student"><i class="fas fa-user-graduate"></i> Student</span>
                        @endif

                        <!-- Notifications -->
                        <div class="dropdown" id="notificationDropdownContainer">
                            <a class="notification-bell" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="notificationBell">
                                <i class="fas fa-bell"></i>
                                @if(isset($unreadCount) && $unreadCount > 0)
                                    <span class="notification-badge" id="notificationCount">{{ $unreadCount }}</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="width: 340px; max-height: 420px; overflow-y: auto; padding: 0;" id="notificationList">
                                <li class="px-3 py-3 border-bottom d-flex justify-content-between align-items-center" style="background: #f8fafc; border-radius: 12px 12px 0 0;">
                                    <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Notifications</h6>
                                    @if(isset($unreadCount) && $unreadCount > 0)
                                        <span class="badge rounded-pill" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); font-size: 0.7rem; padding: 5px 10px;">{{ $unreadCount }} New</span>
                                    @endif
                                </li>
                                @if(isset($unreadNotifications) && count($unreadNotifications) > 0)
                                    @foreach($unreadNotifications as $notification)
                                        <li class="border-bottom">
                                            <a href="{{ $notification->link ?? 'javascript:void(0)' }}" class="dropdown-item p-3 text-wrap notification-item d-flex align-items-start" data-id="{{ $notification->id }}" style="white-space: normal;">
                                                <div class="me-3" style="min-width: 38px; height: 38px; background: linear-gradient(135deg, #e0e7ff, #c7d2fe); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #4f46e5; font-size: 0.95rem;">
                                                    <i class="{{ $notification->icon ?? 'fas fa-bell' }}"></i>
                                                </div>
                                                <div style="flex: 1;">
                                                    <div class="fw-bold text-dark mb-1" style="font-size: 0.85rem; line-height: 1.3;">{{ $notification->title }}</div>
                                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1.4;">{{ $notification->message }}</div>
                                                    <div class="text-muted mt-1" style="font-size: 0.65rem;">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</div>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="p-4 text-center text-muted">
                                        <div style="font-size: 2rem; margin-bottom: 8px; opacity: 0.3;"><i class="fas fa-bell-slash"></i></div>
                                        <p class="mb-0" style="font-size: 0.85rem;">No new notifications</p>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <!-- User Dropdown -->
                        <div class="dropdown">
                            <a class="user-dropdown-btn dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" style="text-decoration: none;">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                {{ Auth::user()->username }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                                <li>
                                    <a href="{{ route('password.change') }}" class="dropdown-item"><i class="fas fa-key me-2 text-muted" style="font-size: 0.85rem;"></i> Change Password</a>
                                </li>
                                <li><hr class="dropdown-divider mx-2"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2" style="font-size: 0.85rem;"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Content Body -->
            <div class="content-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>

            <!-- Footer gradient line -->
            <div class="footer-gradient"></div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Script -->
    <script>
        $(document).ready(function () {
            // Sidebar toggle with smooth animation
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
                // Rotate the icon
                $(this).find('i').toggleClass('fa-bars fa-times');
            });

            // Initialize DataTables
            $('.datatable').DataTable();

            // Move all modals to body level to prevent stacking context issues
            $('.modal').appendTo('body');

            // Page loading bar for navigation
            $('a[href]').on('click', function(e) {
                var href = $(this).attr('href');
                if (href && href !== '#' && href !== 'javascript:void(0)' && !href.startsWith('#') && !$(this).hasClass('dropdown-toggle') && !$(this).hasClass('notification-item')) {
                    $('#pageLoader').addClass('active');
                }
            });

            // Scroll reveal animation
            function revealOnScroll() {
                var reveals = document.querySelectorAll('.reveal-on-scroll');
                reveals.forEach(function(el) {
                    var windowHeight = window.innerHeight;
                    var elementTop = el.getBoundingClientRect().top;
                    if (elementTop < windowHeight - 80) {
                        el.classList.add('revealed');
                    }
                });
            }

            window.addEventListener('scroll', revealOnScroll);
            revealOnScroll(); // initial check

            // Handle Notification Click / Mark as Read
            $(document).on('click', '.notification-item', function(e) {
                var notificationId = $(this).data('id');
                var $item = $(this);
                var targetUrl = $item.attr('href');

                if (notificationId) {
                    e.preventDefault();
                    $.ajax({
                        url: '/notifications/' + notificationId + '/read',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (targetUrl && targetUrl !== 'javascript:void(0)') {
                                window.location.href = targetUrl;
                            } else {
                                $item.closest('li').remove();
                                var currentCount = parseInt($('#notificationCount').text()) || 0;
                                if (currentCount > 1) {
                                    $('#notificationCount').text(currentCount - 1);
                                } else {
                                    $('#notificationCount').remove();
                                    $('#notificationList').html('<li class="p-4 text-center text-muted"><div style="font-size: 2rem; margin-bottom: 8px; opacity: 0.3;"><i class="fas fa-bell-slash"></i></div><p class="mb-0" style="font-size: 0.85rem;">No new notifications</p></li>');
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error('Failed to mark notification as read:', xhr);
                            if (targetUrl && targetUrl !== 'javascript:void(0)') {
                                window.location.href = targetUrl;
                            }
                        }
                    });
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
