@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
    <div>
        <h2 class="h3 mb-1 fw-bold" style="letter-spacing: -0.5px;">Admin Dashboard</h2>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Overview of your college management system</p>
    </div>
    <span class="role-badge role-badge-admin"><i class="fas fa-user-shield me-1"></i> Admin</span>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <a href="{{ route('admin.students') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-primary shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85; font-size: 0.75rem; letter-spacing: 0.5px;">Total Students</div>
                    <div class="h3 mb-0 font-weight-bold stat-value">{{ $total_students }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-user-graduate icon-breathe"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4 fade-in-up" style="animation-delay: 0.2s;">
        <a href="{{ route('admin.faculty') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-success shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85; font-size: 0.75rem; letter-spacing: 0.5px;">Total Faculty</div>
                    <div class="h3 mb-0 font-weight-bold stat-value">{{ $total_faculty }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-chalkboard-teacher icon-breathe"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4 fade-in-up" style="animation-delay: 0.3s;">
        <a href="{{ route('admin.courses') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-warning shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85; font-size: 0.75rem; letter-spacing: 0.5px;">Total Courses</div>
                    <div class="h3 mb-0 font-weight-bold stat-value">{{ $total_courses }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-book icon-breathe"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4 fade-in-up" style="animation-delay: 0.4s;">
        <a href="{{ route('admin.departments') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-danger shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85; font-size: 0.75rem; letter-spacing: 0.5px;">Departments</div>
                    <div class="h3 mb-0 font-weight-bold stat-value">{{ $total_departments }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-building icon-breathe"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4 fade-in-up" style="animation-delay: 0.5s;">
        <div class="premium-card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center gap-2" style="border-bottom: 2px solid rgba(99,102,241,0.1);">
                <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.8rem;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h6 class="m-0 fw-bold" style="color: #334155;">Attendance Overview</h6>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4 fade-in-up" style="animation-delay: 0.6s;">
        <div class="premium-card shadow h-100">
            <div class="card-header py-3 d-flex align-items-center gap-2" style="border-bottom: 2px solid rgba(16,185,129,0.1);">
                <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.8rem;">
                    <i class="fas fa-bolt"></i>
                </div>
                <h6 class="m-0 fw-bold" style="color: #334155;">Quick Actions</h6>
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('admin.students') }}" class="btn btn-sm btn-primary text-start"><i class="fas fa-user-plus me-2"></i>Manage Students</a>
                <a href="{{ route('admin.faculty') }}" class="btn btn-sm btn-primary text-start"><i class="fas fa-chalkboard-teacher me-2"></i>Manage Faculty</a>
                <a href="{{ route('admin.notices') }}" class="btn btn-sm btn-primary text-start"><i class="fas fa-bullhorn me-2"></i>Post Notice</a>
                <a href="{{ route('admin.exams') }}" class="btn btn-sm btn-primary text-start"><i class="fas fa-laptop-code me-2"></i>Manage Exams</a>
                <a href="{{ route('admin.reports') }}" class="btn btn-sm btn-primary text-start"><i class="fas fa-chart-bar me-2"></i>View Reports</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById("attendanceChart").getContext('2d');

        // Create gradient fill
        var gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.15)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.01)');

        var attendanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                datasets: [{
                    label: "Attendance %",
                    data: [85, 90, 88, 92, 87, 80],
                    backgroundColor: gradient,
                    borderColor: '#6366f1',
                    borderWidth: 2.5,
                    pointRadius: 5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 2.5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#6366f1',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    pointHitRadius: 10,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(0,0,0,0.04)',
                            drawBorder: false
                        },
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            color: '#94a3b8'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { family: 'Inter', size: 11, weight: '500' },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
