@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Edit Student</h2>
    <a href="{{ route('admin.students') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Students</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Student Details</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $student->first_name) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $student->last_name) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $student->user->email) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Enrollment No</label>
                    <input type="text" name="enrollment_no" class="form-control" value="{{ old('enrollment_no', $student->enrollment_no) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-control" required>
                        <option value="">Select Course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $student->course_id) == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Current Semester</label>
                    <input type="number" name="current_semester" class="form-control" min="1" max="10" value="{{ old('current_semester', $student->current_semester) }}" required>
                </div>
                
                <h5 class="mt-4 mb-3">Login Credentials</h5>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $student->user->username) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password <small class="text-muted">(leave blank to keep current password)</small></label>
                    <input type="password" name="password" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update Student</button>
        </form>
    </div>
</div>
@endsection
