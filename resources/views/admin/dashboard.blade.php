@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
    <h2 class="h3 mb-0 text-gray-800">Admin Dashboard</h2>
    <span class="badge bg-premium-danger px-3 py-2 rounded-pill"><i class="fas fa-user-shield me-1"></i> Logged in as Admin</span>
</div>

<div class="row fade-in-up delay-1">
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.students') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-primary shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85;">Total Students</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $total_students }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.faculty') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-success shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85;">Total Faculty</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $total_faculty }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.courses') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-warning shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85;">Total Courses</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $total_courses }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-book"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.departments') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-danger shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85;">Departments</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $total_departments }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-building"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Attendance Overview</h6>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById("attendanceChart").getContext('2d');
        var attendanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                datasets: [{
                    label: "Attendance %",
                    data: [85, 90, 88, 92, 87, 80],
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    });
</script>
@endsection
