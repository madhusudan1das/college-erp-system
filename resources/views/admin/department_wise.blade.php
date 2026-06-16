@extends('layouts.app')

@section('title', 'Department Directory')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Department Directory</h2>
</div>

<!-- Department Selector -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row align-items-end">
            <div class="col-md-8 mb-2">
                <label class="form-label font-weight-bold">Select Department:</label>
                <select name="department_id" class="form-control" onchange="this.form.submit()">
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $selected_dept_id == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }} ({{ $dept->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sync"></i> Refresh Directory</button>
            </div>
        </form>
    </div>
</div>

@if ($selected_dept)
    <!-- Stats widgets for the selected department -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="stat-card bg-success-grad shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1">Faculty Members</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $faculty_list->count() }}</div>
                    <small class="text-white-50">Assigned staff in {{ $selected_dept->code }}</small>
                </div>
                <div class="icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="stat-card bg-primary-grad shadow h-100 py-2">
                <div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1">Enrolled Students</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $student_list->count() }}</div>
                    <small class="text-white-50">Students enrolled in {{ $selected_dept->code }} courses</small>
                </div>
                <div class="icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Card -->
    <div class="row">
        <!-- Faculty list -->
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chalkboard-teacher me-2"></i> Faculty List ({{ $selected_dept->code }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered datatable" width="100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($faculty_list as $faculty)
                                    <tr>
                                        <td>{{ $faculty->first_name }} {{ $faculty->last_name }}</td>
                                        <td>{{ $faculty->user->email }}</td>
                                        <td>{{ $faculty->phone ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('admin.faculty.edit', $faculty->id) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i> Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No faculty members found in this department.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student list -->
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-graduate me-2"></i> Students List ({{ $selected_dept->code }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered datatable" width="100%">
                            <thead>
                                <tr>
                                    <th>Enrollment No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Course</th>
                                    <th>Semester</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($student_list as $student)
                                    <tr>
                                        <td>{{ $student->enrollment_no }}</td>
                                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                        <td>{{ $student->user->email }}</td>
                                        <td>{{ $student->course->name }}</td>
                                        <td>Semester {{ $student->current_semester }}</td>
                                        <td>
                                            <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i> Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No students found in this department.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info">Please select a department to display directory list.</div>
@endif
@endsection
