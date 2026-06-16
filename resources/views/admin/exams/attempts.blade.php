@extends('layouts.app')

@section('title', 'Exam Attempts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Exam Attempts: {{ $exam->title }} (Admin Master)</h2>
    <a href="{{ route('admin.exams') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Exams</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-light">
        <h6 class="m-0 font-weight-bold text-primary">Student Attempts Log</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Enrollment No</th>
                        <th>Student Name</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Score Obtained</th>
                        <th>Total Questions</th>
                        <th>Percentage</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attempts as $attempt)
                    <tr>
                        <td>{{ $attempt->student->enrollment_no }}</td>
                        <td>{{ $attempt->student->first_name }} {{ $attempt->student->last_name }}</td>
                        <td>{{ $attempt->start_time->format('d M Y h:i A') }}</td>
                        <td>{{ $attempt->end_time ? $attempt->end_time->format('d M Y h:i A') : '-' }}</td>
                        <td>{{ $attempt->score }}</td>
                        <td>{{ $attempt->total_questions }}</td>
                        <td>
                            @if ($attempt->total_questions > 0)
                                <strong class="text-primary">{{ number_format(($attempt->score / $attempt->total_questions) * 100, 1) }}%</strong>
                            @else
                                0%
                            @endif
                        </td>
                        <td>
                            @if ($attempt->status == 'submitted')
                                <span class="badge bg-success">Submitted</span>
                            @else
                                <span class="badge bg-warning text-dark">Ongoing</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
