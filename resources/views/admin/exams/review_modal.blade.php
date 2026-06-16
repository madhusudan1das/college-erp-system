<div class="modal-header bg-light">
    <h5 class="modal-title fw-bold text-dark" id="reviewAttemptModalLabel">
        <i class="fas fa-poll text-primary me-2"></i>Attempt Review: {{ $attempt->student->first_name }} {{ $attempt->student->last_name }}
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body bg-light" style="max-height: 70vh; overflow-y: auto;">
    <!-- Attempt Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 text-center">
                <span class="text-muted small fw-semibold uppercase d-block mb-1">SCORE</span>
                <h4 class="fw-bold text-primary mb-0">{{ $attempt->score }} / {{ $attempt->total_questions }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 text-center">
                <span class="text-muted small fw-semibold uppercase d-block mb-1">PERCENTAGE</span>
                <h4 class="fw-bold text-success mb-0">
                    @if($attempt->total_questions > 0)
                        {{ number_format(($attempt->score / $attempt->total_questions) * 100, 1) }}%
                    @else
                        0.0%
                    @endif
                </h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 text-center">
                <span class="text-muted small fw-semibold uppercase d-block mb-1">DURATION</span>
                <h4 class="fw-bold text-secondary mb-0">
                    @if($attempt->end_time)
                        {{ $attempt->start_time->diffInMinutes($attempt->end_time) }} Mins
                    @else
                        -
                    @endif
                </h4>
            </div>
        </div>
    </div>

    <!-- Question list -->
    @if($questions->isEmpty())
        <div class="text-center py-4 text-muted">
            <i class="fas fa-info-circle fa-2x mb-2"></i>
            <p class="mb-0">No questions found in this exam.</p>
        </div>
    @else
        @foreach($questions as $index => $q)
            @php
                $userAns = $answers->get($q->id);
                $selectedOpt = $userAns ? $userAns->selected_option : null;
                $isCorrect = $userAns ? $userAns->is_correct : false;
            @endphp
            <div class="card mb-3 border-0 shadow-sm bg-white" style="border-left: 5px solid {{ $selectedOpt ? ($isCorrect ? '#198754' : '#dc3545') : '#ffc107' }} !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="fw-bold text-dark mb-0">Question {{ $index + 1 }}</h6>
                        @if($selectedOpt)
                            @if($isCorrect)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fas fa-check me-1"></i> Correct</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fas fa-times me-1"></i> Incorrect</span>
                            @endif
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="fas fa-exclamation-triangle me-1"></i> Unattempted</span>
                        @endif
                    </div>
                    
                    <p class="text-dark fw-medium mb-3">{!! nl2br(e($q->question_text)) !!}</p>
                    
                    <div class="row g-2 mb-3">
                        <!-- Option A -->
                        <div class="col-md-6">
                            <div class="p-2 border rounded d-flex align-items-center {{ $q->correct_option === 'A' ? 'bg-success-subtle border-success text-success fw-semibold' : ($selectedOpt === 'A' ? 'bg-danger-subtle border-danger text-danger fw-semibold' : 'bg-light') }}" style="min-height: 40px;">
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
                            <div class="p-2 border rounded d-flex align-items-center {{ $q->correct_option === 'B' ? 'bg-success-subtle border-success text-success fw-semibold' : ($selectedOpt === 'B' ? 'bg-danger-subtle border-danger text-danger fw-semibold' : 'bg-light') }}" style="min-height: 40px;">
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
                            <div class="p-2 border rounded d-flex align-items-center {{ $q->correct_option === 'C' ? 'bg-success-subtle border-success text-success fw-semibold' : ($selectedOpt === 'C' ? 'bg-danger-subtle border-danger text-danger fw-semibold' : 'bg-light') }}" style="min-height: 40px;">
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
                            <div class="p-2 border rounded d-flex align-items-center {{ $q->correct_option === 'D' ? 'bg-success-subtle border-success text-success fw-semibold' : ($selectedOpt === 'D' ? 'bg-danger-subtle border-danger text-danger fw-semibold' : 'bg-light') }}" style="min-height: 40px;">
                                <span class="badge {{ $q->correct_option === 'D' ? 'bg-success' : ($selectedOpt === 'D' ? 'bg-danger' : 'bg-secondary') }} me-2">D</span> {{ $q->option_d }}
                                @if($q->correct_option === 'D')
                                    <i class="fas fa-check-circle ms-auto text-success"></i>
                                @elseif($selectedOpt === 'D')
                                    <i class="fas fa-times-circle ms-auto text-danger"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top small text-muted">
                        <div>
                            <i class="fas fa-user me-1"></i> Student Answer: 
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
<div class="modal-footer bg-light">
    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
</div>
