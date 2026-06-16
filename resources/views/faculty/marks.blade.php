@extends('layouts.app')

@section('title', 'Student Marks Entry')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Student Marks Management</h2>
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
                <label class="form-label">Exam Type</label>
                <select name="exam_type" class="form-control" required>
                    <option value="">Select Exam Type</option>
                    <option value="internal" {{ $selected_exam == 'internal' ? 'selected' : '' }}>Internal</option>
                    <option value="external" {{ $selected_exam == 'external' ? 'selected' : '' }}>External</option>
                    <option value="online_exam" {{ $selected_exam == 'online_exam' ? 'selected' : '' }}>Online Exam</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sync"></i> Load Students</button>
            </div>
        </form>
    </div>
</div>

@if ($selected_subject && $selected_exam)
    @if (count($students) > 0)
        @php
            $max_marks_default = 100;
            if ($marksMap->isNotEmpty()) {
                $max_marks_default = $marksMap->first()->max_marks;
            }
        @endphp
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-primary">Student List</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('faculty.marks.store') }}">
                    @csrf
                    <input type="hidden" name="subject_id" value="{{ $selected_subject }}">
                    <input type="hidden" name="exam_type" value="{{ $selected_exam }}">
                    
                    <div class="row align-items-center mb-3">
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Maximum Marks for this Exam:</label>
                            <input type="number" name="max_marks" class="form-control" value="{{ $max_marks_default }}" required min="1">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" width="100%">
                            <thead>
                                <tr>
                                    <th>Enrollment No</th>
                                    <th>Name</th>
                                    <th>Marks Obtained</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $student)
                                    @php
                                        $markRecord = $marksMap->get($student->id);
                                        $obtained = $markRecord ? $markRecord->marks_obtained : '';
                                    @endphp
                                    <tr>
                                        <td>{{ $student->enrollment_no }}</td>
                                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                        <td>
                                            <input type="number" name="marks[{{ $student->id }}]" class="form-control" step="0.01" min="0" value="{{ $obtained }}" placeholder="Enter marks">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-success mt-3"><i class="fas fa-save"></i> Save Marks</button>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-info">No students found for this subject/course in the current semester.</div>
    @endif
@endif
@endsection
