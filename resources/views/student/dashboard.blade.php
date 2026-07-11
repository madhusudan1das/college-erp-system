@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 fade-in-up">
    <div>
        <h2 class="h3 mb-1 fw-bold" style="letter-spacing: -0.5px;">Student Dashboard</h2>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Welcome back, <strong>{{ $student->first_name }} {{ $student->last_name }}</strong> 👋</p>
    </div>
    <span class="role-badge role-badge-student"><i class="fas fa-user-graduate me-1"></i> Student</span>
</div>

<!-- Course Info Card -->
<div class="row mb-4">
    <div class="col-md-12 fade-in-up" style="animation-delay: 0.1s;">
        <div class="premium-card p-4" style="border-left: 5px solid #6366f1 !important;">
            <h5 class="fw-bold mb-3" style="color: #334155; font-size: 1rem;"><i class="fas fa-university me-2" style="color: #6366f1;"></i>Course & Semester Details</h5>
            <div class="row">
                <div class="col-md-4 mb-2 mb-md-0">
                    <span class="text-muted small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Course</span>
                    <div class="fw-bold" style="color: #1e293b;">{{ $student->course->name }}</div>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <span class="text-muted small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Current Semester</span>
                    <div class="fw-bold" style="color: #1e293b;">Semester {{ $student->current_semester }}</div>
                </div>
                <div class="col-md-4">
                    <span class="text-muted small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Enrollment No.</span>
                    <div class="fw-bold" style="color: #1e293b;">{{ $student->enrollment_no }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row">
    <div class="col-xl-4 col-md-6 mb-4 fade-in-up" style="animation-delay: 0.2s;">
        <a href="{{ route('student.materials') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-primary shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85; font-size: 0.75rem; letter-spacing: 0.5px;">My Semester Subjects</div>
                    <div class="h3 mb-0 font-weight-bold stat-value">{{ $subjects_count }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-book-open icon-breathe"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-4 col-md-6 mb-4 fade-in-up" style="animation-delay: 0.3s;">
        <a href="{{ route('student.assignments') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-success shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85; font-size: 0.75rem; letter-spacing: 0.5px;">Semester Assignments</div>
                    <div class="h3 mb-0 font-weight-bold stat-value">{{ $assignments_count }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-tasks icon-breathe"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-4 col-md-12 mb-4 fade-in-up" style="animation-delay: 0.4s;">
        <a href="{{ route('student.exams') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-warning shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85; font-size: 0.75rem; letter-spacing: 0.5px;">Active Online Exams</div>
                    <div class="h3 mb-0 font-weight-bold stat-value">{{ $exams_count }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-laptop-code icon-breathe"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Notice Board -->
<div class="row mt-2">
    <div class="col-lg-12 fade-in-up" style="animation-delay: 0.5s;">
        <div class="premium-card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center" style="border-bottom: 2px solid rgba(99,102,241,0.1);">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.8rem;">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h6 class="m-0 fw-bold" style="color: #334155;">Notice Board Announcements</h6>
                </div>
                <span class="badge rounded-pill" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); padding: 6px 12px; font-size: 0.75rem;">{{ $notices->count() }} Notices</span>
            </div>
            <div class="card-body">
                @if ($notices->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-info-circle fa-2x mb-3" style="opacity: 0.3;"></i>
                        <p class="mb-0">No announcements posted yet.</p>
                    </div>
                @else
                    <div class="list-group">
                        @foreach ($notices as $index => $notice)
                            <div class="list-group-item list-group-item-action flex-column align-items-start mb-3 rounded-3 shadow-sm glow-primary fade-in-up" style="border-top: 1px solid rgba(0,0,0,0.03); border-right: 1px solid rgba(0,0,0,0.03); border-bottom: 1px solid rgba(0,0,0,0.03); animation-delay: {{ 0.6 + ($index * 0.1) }}s;">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 fw-bold" style="color: #334155;">{{ $notice->title }}</h6>
                                    <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $notice->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="my-2" style="color: #475569; font-size: 0.9rem;">{{ $notice->content }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-light">
                                    <small class="text-muted"><i class="fas fa-user-edit me-1"></i>By: {{ $notice->creator->username }}</small>
                                    @if ($notice->attachment)
                                        <a href="{{ asset($notice->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-paperclip me-1"></i> Attachment</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
