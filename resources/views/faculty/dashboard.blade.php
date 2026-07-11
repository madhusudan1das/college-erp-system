@extends('layouts.app')

@section('title', 'Faculty Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 fade-in-up">
    <div>
        <h2 class="h3 mb-1 fw-bold" style="letter-spacing: -0.5px;">Faculty Dashboard</h2>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Welcome back, <strong>{{ $faculty->first_name }} {{ $faculty->last_name }}</strong> 👋</p>
    </div>
    <span class="role-badge role-badge-faculty"><i class="fas fa-chalkboard-teacher me-1"></i> Faculty</span>
</div>

<!-- Stat Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <a href="{{ route('faculty.materials') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-primary shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85; font-size: 0.75rem; letter-spacing: 0.5px;">Assigned Subjects</div>
                    <div class="h3 mb-0 font-weight-bold stat-value">{{ $assigned_subjects }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-book-open icon-breathe"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4 fade-in-up" style="animation-delay: 0.2s;">
        <a href="{{ route('faculty.assignments') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-success shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85; font-size: 0.75rem; letter-spacing: 0.5px;">Assignments</div>
                    <div class="h3 mb-0 font-weight-bold stat-value">{{ $assignments_count }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-tasks icon-breathe"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4 fade-in-up" style="animation-delay: 0.3s;">
        <a href="{{ route('faculty.exams') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-warning shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85; font-size: 0.75rem; letter-spacing: 0.5px;">Exams</div>
                    <div class="h3 mb-0 font-weight-bold stat-value">{{ $exams_count }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-laptop-code icon-breathe"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4 fade-in-up" style="animation-delay: 0.4s;">
        <a href="{{ route('faculty.notices') }}" class="text-decoration-none text-white h-100 d-block">
            <div class="premium-stat-card bg-premium-danger shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.85; font-size: 0.75rem; letter-spacing: 0.5px;">Notices</div>
                    <div class="h3 mb-0 font-weight-bold stat-value">{{ $notices_count }}</div>
                </div>
                <div class="icon" style="font-size: 2.5rem; opacity: 0.65;">
                    <i class="fas fa-bullhorn icon-breathe"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Today's Schedule -->
<div class="row">
    <div class="col-lg-12 fade-in-up" style="animation-delay: 0.5s;">
        <div class="premium-card shadow mb-4" style="border: 1px solid rgba(0, 150, 136, 0.1) !important;">
            <div class="card-header py-3 d-flex justify-content-between align-items-center" style="border-bottom: 2px solid rgba(0,150,136,0.1);">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #14b8a6, #0d9488); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.8rem;">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <h6 class="m-0 fw-bold" style="color: #334155;">Today's Class Schedule ({{ date('l') }})</h6>
                </div>
                <a href="{{ route('faculty.timetable') }}" class="btn btn-sm btn-primary"><i class="fas fa-calendar-alt me-1"></i>Full Timetable</a>
            </div>
            <div class="card-body">
                @if($today_classes->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-calendar-check fa-3x mb-3" style="color: #14b8a6; opacity: 0.3;"></i>
                        <p class="mb-0 fw-medium">No classes scheduled for today. Have a great day! 🎉</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="color: #64748b; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Time</th>
                                    <th style="color: #64748b; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Course</th>
                                    <th style="color: #64748b; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Semester</th>
                                    <th style="color: #64748b; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Subject</th>
                                    <th style="color: #64748b; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($today_classes as $index => $cls)
                                    <tr class="fade-in-up" style="animation-delay: {{ 0.6 + ($index * 0.08) }}s;">
                                        <td>
                                            <span class="badge py-2 px-3 fw-bold" style="background-color: rgba(20, 184, 166, 0.1); color: #0d9488; border-radius: 8px;">
                                                <i class="far fa-clock me-1"></i>
                                                {{ date('h:i A', strtotime($cls->start_time)) }} - {{ date('h:i A', strtotime($cls->end_time)) }}
                                            </span>
                                        </td>
                                        <td><span class="fw-bold text-dark">{{ $cls->course->name ?? 'N/A' }}</span></td>
                                        <td>Semester {{ $cls->semester }}</td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $cls->subject->name }}</div>
                                            <small class="text-muted">Code: {{ $cls->subject->code }}</small>
                                        </td>
                                        <td>
                                            @if($cls->room)
                                                <span class="badge px-2 py-1" style="background-color: rgba(40, 167, 69, 0.1); color: #28a745; border-radius: 6px;"><i class="fas fa-map-marker-alt me-1"></i>{{ $cls->room }}</span>
                                            @else
                                                <span class="text-muted fst-italic">Unassigned</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
