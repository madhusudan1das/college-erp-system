@extends('layouts.app')

@section('title', 'Assignment Submissions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Submissions: {{ $assignment->title }}</h2>
    <a href="{{ route('faculty.assignments') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Assignments</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-light">
        <h6 class="m-0 font-weight-bold text-primary">Submissions List</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Enrollment No</th>
                        <th>Student Name</th>
                        <th>Submitted At</th>
                        <th>File</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($submissions as $sub)
                    <tr>
                        <td>{{ $sub->student->enrollment_no }}</td>
                        <td>{{ $sub->student->first_name }} {{ $sub->student->last_name }}</td>
                        <td data-sort="{{ strtotime($sub->submitted_at) }}">
                            {{ date('d M Y h:i A', strtotime($sub->submitted_at)) }}
                        </td>
                        <td>
                            <a href="{{ asset($sub->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-download"></i> Download</a>
                        </td>
                        <td>
                            @if ($sub->status == 'graded')
                                <span class="badge bg-success">Graded</span>
                            @elseif ($sub->status == 'submitted')
                                <span class="badge bg-primary">Submitted</span>
                            @else
                                <span class="badge bg-warning text-dark">Late</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('faculty.assignments.grade', $sub->id) }}" method="POST" class="row g-2 align-items-center">
                                @csrf
                                <div class="col-auto">
                                    <select name="status" class="form-select form-select-sm" required>
                                        <option value="submitted" {{ $sub->status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                        <option value="graded" {{ $sub->status == 'graded' ? 'selected' : '' }}>Graded</option>
                                        <option value="late" {{ $sub->status == 'late' ? 'selected' : '' }}>Late</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
