<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'College ERP')</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- Premium Stylesheet -->
    <link href="{{ asset('css/premium.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #2c3e50;
            color: #fff;
            transition: all 0.3s;
            min-height: 100vh;
        }
        #sidebar.active {
            margin-left: -250px;
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: #1a252f;
        }
        #sidebar ul.components {
            padding: 20px 0;
        }
        #sidebar ul li a {
            padding: 15px 20px;
            font-size: 1.1em;
            display: block;
            color: #ecf0f1;
            text-decoration: none;
            transition: 0.3s;
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
        }
        #content {
            width: 100%;
            padding: 20px;
            transition: all 0.3s;
        }
        .navbar {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #f0f0f0;
            font-weight: 600;
            border-radius: 10px 10px 0 0 !important;
        }
        .stat-card {
            color: #fff;
            border-radius: 10px;
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
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header d-flex flex-column align-items-center justify-content-center py-4 text-center">
                <h4 class="mb-1 fw-bold text-white">College ERP</h4>
                @if (Auth::user()->role->name === 'admin')
                    <span class="badge bg-danger text-uppercase fw-bold shadow-sm" style="font-size: 0.75rem; letter-spacing: 0.5px;">Admin Panel</span>
                @elseif (Auth::user()->role->name === 'faculty')
                    <span class="badge bg-primary text-uppercase fw-bold shadow-sm" style="font-size: 0.75rem; letter-spacing: 0.5px;">Faculty Panel</span>
                @elseif (Auth::user()->role->name === 'student')
                    <span class="badge bg-success text-uppercase fw-bold shadow-sm" style="font-size: 0.75rem; letter-spacing: 0.5px;">Student Portal</span>
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
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-outline-secondary">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="ms-auto d-flex align-items-center">
                        @if (Auth::user()->role->name === 'admin')
                            <span class="badge bg-premium-danger px-3 py-2 me-3 rounded-pill shadow-sm"><i class="fas fa-user-shield me-1"></i> Admin</span>
                        @elseif (Auth::user()->role->name === 'faculty')
                            <span class="badge bg-premium-primary px-3 py-2 me-3 rounded-pill shadow-sm"><i class="fas fa-chalkboard-teacher me-1"></i> Faculty</span>
                        @elseif (Auth::user()->role->name === 'student')
                            <span class="badge bg-premium-success px-3 py-2 me-3 rounded-pill shadow-sm"><i class="fas fa-user-graduate me-1"></i> Student</span>
                        @endif

                        <!-- Notifications Dropdown -->
                        <div class="dropdown me-3" id="notificationDropdownContainer">
                            <a class="nav-link text-dark position-relative p-1" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="notificationBell">
                                <i class="fas fa-bell fs-5"></i>
                                @if(isset($unreadCount) && $unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount" style="font-size: 0.6rem; padding: 0.25em 0.5em;">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-0" style="width: 320px; max-height: 400px; overflow-y: auto;" id="notificationList">
                                <li class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top">
                                    <h6 class="mb-0 fw-bold">Notifications</h6>
                                    @if(isset($unreadCount) && $unreadCount > 0)
                                        <span class="badge bg-primary rounded-pill" style="background-color: #00bcd4 !important;">{{ $unreadCount }} New</span>
                                    @endif
                                </li>
                                @if(isset($unreadNotifications) && count($unreadNotifications) > 0)
                                    @foreach($unreadNotifications as $notification)
                                        <li class="border-bottom">
                                            <a href="{{ $notification->link ?? 'javascript:void(0)' }}" class="dropdown-item p-3 text-wrap notification-item d-flex align-items-start" data-id="{{ $notification->id }}" style="white-space: normal;">
                                                <div class="bg-light p-2 rounded-circle me-3 text-cyan" style="color: #00bcd4 !important; font-size: 1.1rem; min-width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="{{ $notification->icon ?? 'fas fa-bell' }}"></i>
                                                </div>
                                                <div style="flex: 1;">
                                                    <div class="fw-bold text-dark mb-1" style="font-size: 0.85rem; line-height: 1.2;">{{ $notification->title }}</div>
                                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1.3;">{{ $notification->message }}</div>
                                                    <div class="text-muted mt-1" style="font-size: 0.65rem;">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</div>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="p-4 text-center text-muted">
                                        <i class="fas fa-bell-slash mb-2 fs-4 d-block"></i>
                                        No new notifications
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle text-dark fw-medium" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->username }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li>
                                    <a href="{{ route('password.change') }}" class="dropdown-item"><i class="fas fa-key fa-sm fa-fw me-2 text-gray-400"></i> Change Password</a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid py-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
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
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
            $('.datatable').DataTable();

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
                                    $('#notificationList').html('<li class="p-4 text-center text-muted"><i class="fas fa-bell-slash mb-2 fs-4 d-block"></i>No new notifications</li>');
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
