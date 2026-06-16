@extends('layouts.app')

@section('title', 'Student Mentoring Records')

@section('content')
<div class="row">
    <!-- Record Mentoring Form -->
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-users-cog me-2"></i>Record Mentoring Session</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('faculty.mentoring.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Select Student (Department Wise)</label>
                        <select class="form-select" id="student_id" name="student_id" required>
                            <option value="">-- Select Student --</option>
                            @foreach($students as $stud)
                                <option value="{{ $stud->id }}">{{ $stud->first_name }} {{ $stud->last_name }} ({{ $stud->enrollment_no }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="meeting_date" class="form-label">Meeting Date</label>
                        <input type="date" class="form-control" id="meeting_date" name="meeting_date" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Mentoring Notes / Discussion Details</label>
                        <textarea class="form-control" id="notes" name="notes" rows="6" placeholder="Document academic progress, complaints, career guidance discussed, or general feedback..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary-grad w-100"><i class="fas fa-save me-1"></i> Save Record</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Mentoring Records List -->
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-clipboard-list me-2"></i>Past Mentoring Sessions</h5>
            </div>
            <div class="card-body">
                @if($records->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-history fa-3x mb-3 text-secondary"></i>
                        <p class="lead">No mentoring sessions recorded yet.</p>
                    </div>
                @else
                    <div class="list-group">
                        @foreach($records as $record)
                            <div class="list-group-item list-group-item-action flex-column align-items-start border-start border-4 border-primary mb-3 rounded shadow-sm">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h5 class="mb-1 text-primary fw-bold">
                                        {{ $record->student->first_name }} {{ $record->student->last_name }}
                                        <small class="text-muted small">({{ $record->student->enrollment_no }})</small>
                                    </h5>
                                    <span class="badge bg-light text-dark border"><i class="fas fa-calendar-day me-1 text-info"></i> {{ \Carbon\Carbon::parse($record->meeting_date)->format('d M Y') }}</span>
                                </div>
                                <p class="mb-1 text-dark mt-2" style="white-space: pre-line;">{{ $record->notes }}</p>
                                <div class="text-end mt-2">
                                    <small class="text-muted"><i class="fas fa-clock me-1"></i> Logged At: {{ \Carbon\Carbon::parse($record->created_at)->format('d M Y, h:i A') }}</small>
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
