@extends('layouts.app')

@section('title', 'Mark Student Attendance')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Student Attendance</h2>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row align-items-end">
            <div class="col-md-4 mb-3">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-control" required>
                    <option value="">Select Subject</option>
                    @foreach ($subjects as $sub)
                        <option value="{{ $sub->id }}" {{ $selected_subject == $sub->id ? 'selected' : '' }}>
                            {{ $sub->name }} ({{ $sub->code }}) - {{ $sub->course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="{{ $selected_date }}" required max="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-4 mb-3">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sync"></i> Load Students</button>
            </div>
        </form>
    </div>
</div>

@if ($selected_subject)
    @if (count($students) > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Student List</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('faculty.attendance.store') }}">
                    @csrf
                    <input type="hidden" name="subject_id" value="{{ $selected_subject }}">
                    <input type="hidden" name="date" value="{{ $selected_date }}">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" width="100%">
                            <thead>
                                <tr>
                                    <th>Enrollment No</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $student)
                                    @php
                                        $status = $attendanceMap->get($student->id, 'present');
                                    @endphp
                                    <tr>
                                        <td>{{ $student->enrollment_no }}</td>
                                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                        <td>
                                            <select name="attendance[{{ $student->id }}]" class="form-control">
                                                <option value="present" {{ $status == 'present' ? 'selected' : '' }}>Present</option>
                                                <option value="absent" {{ $status == 'absent' ? 'selected' : '' }}>Absent</option>
                                                <option value="late" {{ $status == 'late' ? 'selected' : '' }}>Late</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-success mt-3"><i class="fas fa-save"></i> Save Attendance</button>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-info">No students found for this subject/course in the current semester.</div>
    @endif
@endif
@endsection
