@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Student Dashboard</h2>
    <span class="badge bg-premium-success px-3 py-2 rounded-pill"><i class="fas fa-user-graduate me-1"></i> Logged in as Student</span>
</div>
<h5 class="text-muted mb-4">Welcome, {{ $student->first_name }} {{ $student->last_name }}</h5>

<div class="row mb-4 fade-in-up">
    <div class="col-md-12">
        <div class="premium-card p-4 border-start border-primary" style="border-left-width: 5px !important;">
            <h5 class="fw-bold mb-2 text-primary"><i class="fas fa-university me-2"></i>Course & Semester Details</h5>
            <div class="row">
                <div class="col-md-4 mb-2 mb-md-0">
                    <span class="text-muted small">Course</span>
                    <div class="fw-semibold">{{ $student->course->name }}</div>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <span class="text-muted small">Current Semester</span>
                    <div class="fw-semibold">Semester {{ $student->current_semester }}</div>
                </div>
                <div class="col-md-4">
                    <span class="text-muted small">Enrollment No.</span>
                    <div class="fw-semibold">{{ $student->enrollment_no }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row fade-in-up delay-1">
    <div class="col-xl-4 col-md-6 mb-4">
        <a href="{{ route('student.materials') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-primary shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85;">My Semester Subjects</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $subjects_count }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-xl-4 col-md-6 mb-4">
        <a href="{{ route('student.assignments') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-success shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85;">Semester Assignments</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $assignments_count }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-4 col-md-12 mb-4">
        <a href="{{ route('student.exams') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-warning shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85;">Active Online Exams</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $exams_count }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-laptop-code"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row mt-4 fade-in-up delay-2">
    <!-- Notice Board -->
    <div class="col-lg-12">
        <div class="premium-card shadow mb-4">
            <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-bullhorn me-2"></i>Notice Board Announcements</h6>
                <span class="badge bg-primary-grad px-3 py-2 rounded-pill">{{ $notices->count() }} Notices</span>
            </div>
            <div class="card-body">
                @if ($notices->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-info-circle fa-2x mb-3 text-muted"></i>
                        <p class="mb-0">No announcements posted yet.</p>
                    </div>
                @else
                    <div class="list-group">
                        @foreach ($notices as $notice)
                            <div class="list-group-item list-group-item-action flex-column align-items-start mb-3 rounded shadow-sm glow-primary" style="border-top: 1px solid rgba(0,0,0,0.05); border-right: 1px solid rgba(0,0,0,0.05); border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 text-primary fw-semibold">{{ $notice->title }}</h5>
                                    <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $notice->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="my-2 text-dark">{{ $notice->content }}</p>
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
