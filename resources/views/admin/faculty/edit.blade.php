@extends('layouts.app')

@section('title', 'Edit Faculty')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Edit Faculty</h2>
    <a href="{{ route('admin.faculty') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Faculty</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Faculty Details</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.faculty.update', $faculty->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $faculty->first_name) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $faculty->last_name) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $faculty->user->email) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $faculty->phone) }}">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-control" required>
                        <option value="">Select Department</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $faculty->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <h5 class="mt-4 mb-3">Login Credentials</h5>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $faculty->user->username) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password <small class="text-muted">(leave blank to keep current password)</small></label>
                    <input type="password" name="password" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update Faculty</button>
        </form>
    </div>
</div>
@endsection
