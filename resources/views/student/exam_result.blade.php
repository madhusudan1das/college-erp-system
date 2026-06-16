@extends('layouts.app')

@section('title', 'Exam Result Review')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Exam Results Review: {{ $exam->title }}</h2>
    <a href="{{ route('student.exams') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Exams</a>
</div>

<div class="row">
    <!-- Left Column: Summary Info -->
    <div class="col-lg-4">
        <div class="card shadow mb-4 sticky-top" style="top: 20px; z-index: 100;">
            <div class="card-header py-3 bg-primary text-white text-center">
                <h5 class="m-0 fw-bold">Performance Summary</h5>
            </div>
            <div class="card-body text-center">
                <div class="display-3 fw-bold text-success mb-2">
                    {{ $attempt->score }} / {{ $attempt->total_questions }}
                </div>
                <h6 class="fw-bold mb-4 text-muted">Questions Correct</h6>
                
                @php
                    $percentage = $attempt->total_questions > 0 ? ($attempt->score / $attempt->total_questions) * 100 : 0;
                @endphp

                <div class="progress mb-4" style="height: 25px;">
                    <div class="progress-bar {{ $percentage >= 40 ? 'bg-success' : 'bg-danger' }}" 
                         role="progressbar" 
                         style="width: {{ $percentage }}%;" 
                         aria-valuenow="{{ $percentage }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                         {{ number_format($percentage, 1) }}%
                    </div>
                </div>

                <div class="row border-top pt-3 text-start small">
                    <div class="col-6 mb-2">
                        <strong>Subject:</strong>
                    </div>
                    <div class="col-6 mb-2 text-end text-muted">
                        {{ $exam->subject->name }}
                    </div>
                    
                    <div class="col-6 mb-2">
                        <strong>Start Time:</strong>
                    </div>
                    <div class="col-6 mb-2 text-end text-muted">
                        {{ $attempt->start_time->format('d M Y h:i A') }}
                    </div>
                    
                    <div class="col-6 mb-2">
                        <strong>End Time:</strong>
                    </div>
                    <div class="col-6 mb-2 text-end text-muted">
                        {{ $attempt->end_time ? $attempt->end_time->format('d M Y h:i A') : '-' }}
                    </div>
                    
                    <div class="col-6 mb-2">
                        <strong>Result Status:</strong>
                    </div>
                    <div class="col-6 mb-2 text-end">
                        <span class="badge bg-{{ $percentage >= 40 ? 'success' : 'danger' }}">
                            {{ $percentage >= 40 ? 'Passed' : 'Failed' }}
                        </span>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('student.results') }}" class="btn btn-outline-primary w-100"><i class="fas fa-poll me-1"></i> View Results History</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Question-by-Question review checklist -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-clipboard-check me-2"></i>Question-by-Question Review</h5>
            </div>
            <div class="card-body">
                @if($questions->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <p class="lead">No questions found in this exam.</p>
                    </div>
                @else
                    @foreach($questions as $index => $q)
                        @php
                            $userAns = $answers->get($q->id);
                            $selectedOpt = $userAns ? $userAns->selected_option : null;
                            $isCorrect = $userAns ? $userAns->is_correct : false;
                        @endphp
                        <div class="card mb-4 shadow-sm" style="border-left: 5px solid {{ $isCorrect ? '#198754' : '#dc3545' }} !important;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <h6 class="fw-bold text-dark">Question {{ $index + 1 }}</h6>
                                    @if($isCorrect)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fas fa-check me-1"></i> Correct</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-times me-1"></i> Incorrect</span>
                                    @endif
                                </div>
                                
                                <p class="text-dark fw-medium mb-3">{!! nl2br(e($q->question_text)) !!}</p>
                                
                                <div class="row g-2 mb-3">
                                    <!-- Option A -->
                                    <div class="col-md-6">
                                        <div class="p-2.5 border rounded d-flex align-items-center {{ $q->correct_option === 'A' ? 'bg-success-subtle border-success text-success fw-semibold' : ($selectedOpt === 'A' ? 'bg-danger-subtle border-danger text-danger fw-semibold' : 'bg-light') }}" style="min-height: 48px;">
                                            <span class="badge {{ $q->correct_option === 'A' ? 'bg-success' : ($selectedOpt === 'A' ? 'bg-danger' : 'bg-secondary') }} me-2">A</span> {{ $q->option_a }}
                                            @if($q->correct_option === 'A')
                                                <i class="fas fa-check-circle ms-auto text-success"></i>
                                            @elseif($selectedOpt === 'A')
                                                <i class="fas fa-times-circle ms-auto text-danger"></i>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Option B -->
                                    <div class="col-md-6">
                                        <div class="p-2.5 border rounded d-flex align-items-center {{ $q->correct_option === 'B' ? 'bg-success-subtle border-success text-success fw-semibold' : ($selectedOpt === 'B' ? 'bg-danger-subtle border-danger text-danger fw-semibold' : 'bg-light') }}" style="min-height: 48px;">
                                            <span class="badge {{ $q->correct_option === 'B' ? 'bg-success' : ($selectedOpt === 'B' ? 'bg-danger' : 'bg-secondary') }} me-2">B</span> {{ $q->option_b }}
                                            @if($q->correct_option === 'B')
                                                <i class="fas fa-check-circle ms-auto text-success"></i>
                                            @elseif($selectedOpt === 'B')
                                                <i class="fas fa-times-circle ms-auto text-danger"></i>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Option C -->
                                    <div class="col-md-6">
                                        <div class="p-2.5 border rounded d-flex align-items-center {{ $q->correct_option === 'C' ? 'bg-success-subtle border-success text-success fw-semibold' : ($selectedOpt === 'C' ? 'bg-danger-subtle border-danger text-danger fw-semibold' : 'bg-light') }}" style="min-height: 48px;">
                                            <span class="badge {{ $q->correct_option === 'C' ? 'bg-success' : ($selectedOpt === 'C' ? 'bg-danger' : 'bg-secondary') }} me-2">C</span> {{ $q->option_c }}
                                            @if($q->correct_option === 'C')
                                                <i class="fas fa-check-circle ms-auto text-success"></i>
                                            @elseif($selectedOpt === 'C')
                                                <i class="fas fa-times-circle ms-auto text-danger"></i>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Option D -->
                                    <div class="col-md-6">
                                        <div class="p-2.5 border rounded d-flex align-items-center {{ $q->correct_option === 'D' ? 'bg-success-subtle border-success text-success fw-semibold' : ($selectedOpt === 'D' ? 'bg-danger-subtle border-danger text-danger fw-semibold' : 'bg-light') }}" style="min-height: 48px;">
                                            <span class="badge {{ $q->correct_option === 'D' ? 'bg-success' : ($selectedOpt === 'D' ? 'bg-danger' : 'bg-secondary') }} me-2">D</span> {{ $q->option_d }}
                                            @if($q->correct_option === 'D')
                                                <i class="fas fa-check-circle ms-auto text-success"></i>
                                            @elseif($selectedOpt === 'D')
                                                <i class="fas fa-times-circle ms-auto text-danger"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top small text-muted">
                                    <div>
                                        <i class="fas fa-user me-1"></i> Your Answer: 
                                        @if($selectedOpt)
                                            <strong class="text-{{ $isCorrect ? 'success' : 'danger' }}">Option {{ $selectedOpt }}</strong>
                                        @else
                                            <strong class="text-warning">Not Attempted</strong>
                                        @endif
                                    </div>
                                    <div>
                                        <i class="fas fa-check-double me-1 text-success"></i> Correct Answer: 
                                        <strong class="text-success font-monospace">Option {{ $q->correct_option }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
