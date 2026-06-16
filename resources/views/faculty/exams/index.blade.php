@extends('layouts.app')

@section('title', 'Manage Exams')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Online Exams Management</h2>
</div>

<div class="row">
    <!-- Create Exam Form -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Create New Exam</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('faculty.exams.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Exam Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Midterm Programming Exam">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Filter by Department</label>
                        <select id="exam_dept" class="form-control">
                            <option value="">All Departments</option>
                            @foreach ($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Filter by Semester</label>
                        <select id="exam_sem" class="form-control">
                            <option value="">All Semesters</option>
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}">Semester {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <select name="subject_id" id="subject_id" class="form-control" required>
                            <option value="">Select Subject</option>
                            @foreach ($subjects as $sub)
                                <option value="{{ $sub->id }}" 
                                        data-dept-id="{{ $sub->course->department_id ?? '' }}" 
                                        data-semester="{{ $sub->semester }}">
                                    {{ $sub->name }} ({{ $sub->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (Minutes)</label>
                        <input type="number" name="duration_minutes" class="form-control" min="5" max="180" value="60" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create Exam</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Exam List -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Exam List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Subject</th>
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
                                <td>{{ $exam->subject->name }}</td>
                                <td><strong>{{ $exam->title }}</strong></td>
                                <td>{{ $exam->duration_minutes }} Mins</td>
                                <td>
                                    <span class="badge bg-{{ $exam->status == 'active' ? 'success' : ($exam->status == 'completed' ? 'secondary' : 'warning') }}">
                                        {{ ucfirst($exam->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('faculty.exams.manage', $exam->id) }}" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-list-ol"></i> Manage ({{ $exam->questions()->count() }})
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('faculty.exams.attempts', $exam->id) }}" class="btn btn-sm btn-dark">
                                        <i class="fas fa-user-check"></i> Attempts ({{ $exam->attempts()->count() }})
                                    </a>
                                </td>
                                <td>
                                    <form action="{{ route('faculty.exams.toggle', $exam->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                            <option value="pending" {{ $exam->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="active" {{ $exam->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="completed" {{ $exam->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </form>
                                    <form action="{{ route('faculty.exams.delete', $exam->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this exam?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
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
