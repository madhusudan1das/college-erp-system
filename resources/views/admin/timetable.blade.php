@extends('layouts.app')

@section('title', 'Manage Timetable')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Timetable Management</h2>
</div>

<div class="row">
    <!-- Add Timetable Slot Form -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add Timetable Slot</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.timetable.store') }}" method="POST">
                    @csrf
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
                        <label class="form-label">Subject</label>
                        <select name="subject_id" class="form-control" required>
                            <option value="">Select Subject</option>
                            @foreach ($subjects as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->code }} - {{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Faculty</label>
                        <select name="faculty_id" class="form-control" required>
                            <option value="">Select Faculty</option>
                            @foreach ($facultyList as $fac)
                                <option value="{{ $fac->id }}">{{ $fac->first_name }} {{ $fac->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Day of Week</label>
                        <select name="day_of_week" class="form-control" required>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Start Time</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">End Time</label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Slot</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Timetable List -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Timetable Slots</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Subject</th>
                                <th>Course/Sem</th>
                                <th>Faculty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($timetables as $slot)
                            <tr>
                                <td><strong>{{ $slot->day_of_week }}</strong></td>
                                <td>{{ date('h:i A', strtotime($slot->start_time)) }} - {{ date('h:i A', strtotime($slot->end_time)) }}</td>
                                <td>{{ $slot->subject->name }} ({{ $slot->subject->code }})</td>
                                <td>{{ $slot->course->name }} (Sem {{ $slot->semester }})</td>
                                <td>{{ $slot->faculty->first_name }} {{ $slot->faculty->last_name }}</td>
                                <td>
                                    <form action="{{ route('admin.timetable.delete', $slot->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
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
