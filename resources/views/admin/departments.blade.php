@extends('layouts.app')

@section('title', 'Manage Departments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Departments</h2>
</div>

<div class="row">
    <!-- Add Department Form -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add New Department</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.departments.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Department Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Computer Science & Engineering">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department Code</label>
                        <input type="text" name="code" class="form-control" required placeholder="e.g. CSE">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Department</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Department List -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Department List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Department Name</th>
                                <th>Code</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departments as $dept)
                            <tr>
                                <td>{{ $dept->id }}</td>
                                <td>{{ $dept->name }}</td>
                                <td><span class="badge bg-secondary">{{ $dept->code }}</span></td>
                                <td>
                                    <form action="{{ route('admin.departments.delete', $dept->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department?');">
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
