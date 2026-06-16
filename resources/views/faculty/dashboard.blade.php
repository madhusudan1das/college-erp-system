@extends('layouts.app')

@section('title', 'Faculty Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Faculty Dashboard</h2>
    <span class="badge bg-premium-primary px-3 py-2 rounded-pill"><i class="fas fa-chalkboard-teacher me-1"></i> Logged in as Faculty</span>
</div>
<h5 class="text-muted mb-4">Welcome, {{ $faculty->first_name }} {{ $faculty->last_name }}</h5>

<div class="row fade-in-up">
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('faculty.materials') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-primary shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85;">Assigned Subjects</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $assigned_subjects }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('faculty.assignments') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-success shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85;">Assignments</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $assignments_count }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('faculty.exams') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-warning shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85;">Exams</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $exams_count }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-laptop-code"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('faculty.notices') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-danger shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85;">Notices</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $notices_count }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-bullhorn"></i>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
