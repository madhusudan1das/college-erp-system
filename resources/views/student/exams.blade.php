@extends('layouts.app')

@section('title', 'Online Exams')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Online Exams</h2>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-light">
        <h6 class="m-0 font-weight-bold text-primary">Active Exams</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Exam Title</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($exams as $exam)
                        @php
                            $attempt = $attempts->get($exam->id);
                        @endphp
                        <tr>
                            <td>{{ $exam->subject->name }} ({{ $exam->subject->code }})</td>
                            <td><strong>{{ $exam->title }}</strong></td>
                            <td>{{ $exam->duration_minutes }} Mins</td>
                            <td>
                                @if ($attempt)
                                    @if ($attempt->status == 'submitted')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Ongoing</span>
                                    @endif
                                @else
                                    <span class="badge bg-primary">Available</span>
                                @endif
                            </td>
                            <td>
                                @if ($attempt)
                                    @if ($attempt->status == 'submitted')
                                        <a href="{{ route('student.exams.result', $exam->id) }}" class="btn btn-sm btn-info text-white">
                                            <i class="fas fa-poll"></i> View Score ({{ $attempt->score }} / {{ $attempt->total_questions }})
                                        </a>
                                    @else
                                        <a href="{{ route('student.exams.take', $exam->id) }}" class="btn btn-sm btn-warning text-dark">
                                            <i class="fas fa-laptop-code"></i> Resume Exam
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('student.exams.take', $exam->id) }}" class="btn btn-sm btn-primary" onclick="return confirm('Do you want to start this exam? The timer will start immediately.');">
                                        <i class="fas fa-play"></i> Start Exam
                                    </a>
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
