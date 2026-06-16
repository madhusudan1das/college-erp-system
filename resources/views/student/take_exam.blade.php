<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Exam: {{ $exam->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .timer-header { position: sticky; top: 0; z-index: 1000; background: #fff; border-bottom: 2px solid #e3e6f0; }
        .question-card { margin-bottom: 20px; border-left: 4px solid #4e73df; }
    </style>
</head>
<body>

<div class="timer-header py-3 shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <h4 class="mb-0 fw-bold text-primary">{{ $exam->title }}</h4>
        <div class="fs-5 fw-bold text-danger">
            Time Remaining: <span id="timer">Loading...</span>
        </div>
    </div>
</div>

<div class="container pb-5">
    <form id="examForm" action="{{ route('student.exams.submit', $exam->id) }}" method="POST">
        @csrf
        @foreach ($questions as $index => $q)
            <div class="card shadow-sm question-card">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Q{{ $index + 1 }}: {!! nl2br(e($q->question_text)) !!}</h5>
                    <div class="mt-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]" value="A" id="q{{ $q->id }}a">
                            <label class="form-check-label" for="q{{ $q->id }}a">{{ $q->option_a }}</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]" value="B" id="q{{ $q->id }}b">
                            <label class="form-check-label" for="q{{ $q->id }}b">{{ $q->option_b }}</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]" value="C" id="q{{ $q->id }}c">
                            <label class="form-check-label" for="q{{ $q->id }}c">{{ $q->option_c }}</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]" value="D" id="q{{ $q->id }}d">
                            <label class="form-check-label" for="q{{ $q->id }}d">{{ $q->option_d }}</label>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success btn-lg px-5 shadow" onclick="return confirm('Are you sure you want to submit your answers?');">Submit Exam Now</button>
        </div>
    </form>
</div>

<script>
    var timeRemaining = {{ $time_remaining }};
    var timerElement = document.getElementById('timer');
    var examForm = document.getElementById('examForm');

    function updateTimer() {
        if (timeRemaining <= 0) {
            timerElement.innerHTML = "00:00";
            alert("Time is up! Submitting your exam automatically.");
            examForm.submit();
            return;
        }

        var minutes = Math.floor(timeRemaining / 60);
        var seconds = timeRemaining % 60;
        
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        
        timerElement.innerHTML = minutes + ":" + seconds;
        timeRemaining--;
        setTimeout(updateTimer, 1000);
    }
    
    updateTimer();
</script>

</body>
</html>
