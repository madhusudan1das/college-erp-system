@extends('layouts.app')

@section('title', 'Manage Exams')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Online Exams Management (Admin Master)</h2>
</div>

<div class="row">
    <!-- Create Exam Form -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Create New Exam</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.exams.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Exam Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Midterm Programming Exam">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Filter by Department</label>
                        <select id="exam_dept" class="form-select">
                            <option value="">All Departments</option>
                            @foreach ($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Filter by Semester</label>
                        <select id="exam_sem" class="form-select">
                            <option value="">All Semesters</option>
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}">Semester {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <select name="subject_id" id="subject_id" class="form-select" required>
                            <option value="">Select Subject</option>
                            @foreach ($subjects as $sub)
                                <option value="{{ $sub->id }}" 
                                        data-dept-id="{{ $sub->course->department_id ?? '' }}" 
                                        data-semester="{{ $sub->semester }}">
                                    {{ $sub->name }} ({{ $sub->code }}) @if($sub->faculty) - taught by {{ $sub->faculty->first_name }} {{ $sub->faculty->last_name }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (Minutes)</label>
                        <input type="number" name="duration_minutes" class="form-control" min="5" max="180" value="60" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus-circle me-1"></i> Create Exam</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Exam List -->
    <div class="col-lg-8 mb-4">
        <!-- Dashboard Filters -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-filter me-1"></i> Filter Exam List</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.exams') }}" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">All Departments</option>
                            @foreach ($departments as $d)
                                <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-select">
                            <option value="">All Semesters</option>
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-dark w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">All Exams</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Faculty</th>
                                <th>Title</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Questions</th>
                                <th>Attempts</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($exams as $exam)
                            <tr>
                                <td>
                                    {{ $exam->subject->name }}<br>
                                    <small class="text-muted">{{ $exam->subject->course->department->name ?? 'N/A' }} - Sem {{ $exam->subject->semester }}</small>
                                </td>
                                <td>
                                    @if ($exam->faculty)
                                        {{ $exam->faculty->first_name }} {{ $exam->faculty->last_name }}
                                    @else
                                        <span class="text-danger">Unassigned</span>
                                    @endif
                                </td>
                                <td><strong>{{ $exam->title }}</strong></td>
                                <td>{{ $exam->duration_minutes }} Mins</td>
                                <td>
                                    @if($exam->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($exam->status == 'completed')
                                        <span class="badge bg-secondary">Completed</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $exam->questions()->count() }} Questions</span>
                                    <a href="{{ route('admin.exams.manage', $exam->id) }}" class="btn btn-sm btn-link p-0 ms-2"><i class="fas fa-edit"></i> Manage</a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.exams.attempts', $exam->id) }}" class="btn btn-sm btn-dark">
                                        <i class="fas fa-user-check"></i> Attempts ({{ $exam->attempts()->count() }})
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <form action="{{ route('admin.exams.toggle', $exam->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                <option value="pending" {{ $exam->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="active" {{ $exam->status == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="completed" {{ $exam->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </form>
                                        <form action="{{ route('admin.exams.delete', $exam->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this exam?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
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

@section('scripts')
<script>
$(document).ready(function() {
    const originalOptions = $('#subject_id option').clone();
    function filterSubjects() {
        const deptId = $('#exam_dept').val();
        const sem = $('#exam_sem').val();
        $('#subject_id').html(originalOptions.clone());
        $('#subject_id option').each(function() {
            const option = $(this);
            const optDept = option.data('dept-id');
            const optSem = option.data('semester');
            if (option.val() !== "") {
                const matchDept = !deptId || (optDept == deptId);
                const matchSem = !sem || (optSem == sem);
                if (!matchDept || !matchSem) {
                    option.remove();
                }
            }
        });
    }
    $('#exam_dept, #exam_sem').on('change', filterSubjects);
});
</script>
@endsection
