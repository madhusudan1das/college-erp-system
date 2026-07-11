@extends('layouts.app')

@section('title', 'Exam Results')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Student Exam Results Dashboard</h2>
</div>

<!-- Filters Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-light">
        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-filter me-1"></i> Filter Results</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.results') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select">
                    <option value="">All Departments</option>
                    @foreach ($departments as $d)
                        <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Semester</label>
                <select name="semester" class="form-select">
                    <option value="">All Semesters</option>
                    @for ($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Exam Title</label>
                <select name="exam_id" class="form-select">
                    <option value="">All Exams</option>
                    @foreach ($exams as $e)
                        <option value="{{ $e->id }}" {{ request('exam_id') == $e->id ? 'selected' : '' }}>{{ $e->title }} ({{ $e->subject->code ?? 'N/A' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-search me-1"></i> Filter</button>
                <a href="{{ route('admin.results') }}" class="btn btn-outline-secondary"><i class="fas fa-undo me-1"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Results List -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">Attempts Database</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Student Details</th>
                        <th>Academic Info</th>
                        <th>Exam & Subject</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attempts as $attempt)
                    <tr>
                        <td>
                            <strong>{{ $attempt->student->first_name }} {{ $attempt->student->last_name }}</strong><br>
                            <small class="text-muted">{{ $attempt->student->enrollment_no }}</small>
                        </td>
                        <td>
                            {{ $attempt->student->course->department->name ?? 'N/A' }}<br>
                            <span class="badge bg-light text-dark">Semester {{ $attempt->student->current_semester }}</span>
                        </td>
                        <td>
                            {{ $attempt->exam->title }}<br>
                            <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $attempt->exam->subject->name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <strong>{{ $attempt->score }}</strong> / {{ $attempt->total_questions }}
                        </td>
                        <td>
                            @if ($attempt->total_questions > 0)
                                @php
                                    $percent = ($attempt->score / $attempt->total_questions) * 100;
                                @endphp
                                <strong class="text-{{ $percent >= 40 ? 'success' : 'danger' }}">{{ number_format($percent, 1) }}%</strong>
                            @else
                                0.0%
                            @endif
                        </td>
                        <td>
                            <span class="small text-muted">Started: {{ $attempt->start_time->format('d M Y h:i A') }}</span>
                            @if ($attempt->end_time)
                                <br><span class="small text-muted">Ended: {{ $attempt->end_time->format('d M Y h:i A') }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($attempt->status == 'submitted')
                                <span class="badge bg-success">Submitted</span>
                            @else
                                <span class="badge bg-warning text-dark">Ongoing</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info text-white btn-review-attempt" data-id="{{ $attempt->id }}">
                                <i class="fas fa-eye me-1"></i> Review Attempt
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Container -->
<div class="modal fade" id="reviewAttemptModal" tabindex="-1" aria-labelledby="reviewAttemptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" id="reviewAttemptModalContent">
            <!-- Loaded dynamically via AJAX -->
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading attempt details...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Using event delegation so it works with DataTables (which recreates DOM rows)
    $(document).on('click', '.btn-review-attempt', function() {
        const attemptId = $(this).data('id');
        const modal = $('#reviewAttemptModal');
        const content = $('#reviewAttemptModalContent');
        
        content.html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading attempt details...</p>
            </div>
        `);
        
        modal.modal('show');
        
        $.ajax({
            url: `/admin/exams/attempts/${attemptId}/review`,
            type: 'GET',
            success: function(html) {
                content.html(html);
            },
            error: function(xhr, status, error) {
                content.html(`
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Error Loading Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                        <p class="mb-0">Failed to load the attempt details. Please try again later.</p>
                    </div>
                `);
            }
        });
    });
});
</script>
@endsection
