@extends('layouts.app')

@section('title', 'Manage Courses')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Courses</h2>
</div>

<div class="row">
    <!-- Add Course Form -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add New Course</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.courses.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Course Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. B.Tech Computer Science">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-control" required>
                            <option value="">Select Department</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (Years)</label>
                        <input type="number" name="duration_years" class="form-control" min="1" max="10" required placeholder="e.g. 4">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Course</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Course List -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Course List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Course Name</th>
                                <th>Department</th>
                                <th>Duration</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courses as $course)
                            <tr>
                                <td>{{ $course->id }}</td>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->department->name }}</td>
                                <td>{{ $course->duration_years }} Years</td>
                                <td>
                                    <form action="{{ route('admin.courses.delete', $course->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course?');">
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
