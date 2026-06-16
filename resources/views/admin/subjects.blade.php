@extends('layouts.app')

@section('title', 'Manage Subjects')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Subjects</h2>
</div>

<div class="row">
    <!-- Add Subject Form -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add New Subject</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.subjects.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Subject Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Data Structures">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject Code</label>
                        <input type="text" name="code" class="form-control" required placeholder="e.g. CS-401">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Course</label>
                        <select name="course_id" class="form-control" required>
                            <option value="">Select Course</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Semester</label>
                        <input type="number" name="semester" class="form-control" min="1" max="10" required placeholder="e.g. 4">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assigned Faculty (Optional)</label>
                        <select name="faculty_id" class="form-control">
                            <option value="">Select Faculty</option>
                            @foreach ($facultyList as $fac)
                                <option value="{{ $fac->id }}">{{ $fac->first_name }} {{ $fac->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Subject</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Subject List -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Subject List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Subject Name</th>
                                <th>Course</th>
                                <th>Semester</th>
                                <th>Assigned Faculty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subjects as $subject)
                            <tr>
                                <td><span class="badge bg-dark">{{ $subject->code }}</span></td>
                                <td>{{ $subject->name }}</td>
                                <td>{{ $subject->course->name }}</td>
                                <td>Semester {{ $subject->semester }}</td>
                                <td>{{ $subject->faculty ? $subject->faculty->first_name . ' ' . $subject->faculty->last_name : 'Not Assigned' }}</td>
                                <td>
                                    <form action="{{ route('admin.subjects.delete', $subject->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
