@extends('layouts.app')

@section('title', 'Manage Timetable')

@section('styles')
<style>
    .conflict-alert-card {
        border-radius: 14px;
        border: none;
        box-shadow: 0 4px 18px rgba(0,0,0,0.06);
    }
    .conflict-badge-teacher {
        background: linear-gradient(135deg, #ff5252 0%, #d32f2f 100%);
        color: #fff;
        font-weight: 700;
        font-size: 1.5rem;
        border-radius: 12px;
        padding: 10px 16px;
        min-width: 50px;
        text-align: center;
    }
    .conflict-badge-student {
        background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
        color: #fff;
        font-weight: 700;
        font-size: 1.5rem;
        border-radius: 12px;
        padding: 10px 16px;
        min-width: 50px;
        text-align: center;
    }
    .conflict-item {
        background: rgba(255, 82, 82, 0.04);
        border-left: 4px solid #ff5252;
        border-radius: 0 10px 10px 0;
        padding: 12px 16px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }
    .conflict-item:hover {
        background: rgba(255, 82, 82, 0.08);
        transform: translateX(2px);
    }
    .conflict-item.student-conflict {
        border-left-color: #ff9800;
        background: rgba(255, 152, 0, 0.04);
    }
    .conflict-item.student-conflict:hover {
        background: rgba(255, 152, 0, 0.08);
    }
    .row-conflict-teacher {
        background-color: rgba(255, 82, 82, 0.08) !important;
        border-left: 4px solid #ff5252;
    }
    .row-conflict-student {
        background-color: rgba(255, 152, 0, 0.08) !important;
        border-left: 4px solid #ff9800;
    }
    .filter-btn {
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 5px 14px;
        transition: all 0.3s ease;
    }
    .filter-btn:hover {
        transform: translateY(-1px);
    }
    .filter-btn.active {
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Timetable Management</h2>
</div>

{{-- Conflict Alerts Section --}}
@if(count($facultyConflicts) > 0 || count($studentConflicts) > 0)
<div class="card conflict-alert-card mb-4" style="border-top: 4px solid #ff5252;">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>Scheduling Conflicts Detected
            <span class="badge bg-danger ms-2">{{ count($facultyConflicts) + count($studentConflicts) }}</span>
        </h5>
        <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#conflictDetails">
            <i class="fas fa-chevron-down"></i> Details
        </button>
    </div>
    <div class="collapse show" id="conflictDetails">
        <div class="card-body pt-0">
            <div class="row g-3 mb-3">
                <div class="col-auto">
                    <div class="d-flex align-items-center gap-3">
                        <div class="conflict-badge-teacher">{{ count($facultyConflicts) }}</div>
                        <div>
                            <div class="fw-bold text-danger">Teacher Conflicts</div>
                            <div class="text-muted small">Same teacher, overlapping times</div>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="d-flex align-items-center gap-3">
                        <div class="conflict-badge-student">{{ count($studentConflicts) }}</div>
                        <div>
                            <div class="fw-bold" style="color: #f57c00;">Student Group Conflicts</div>
                            <div class="text-muted small">Same class/semester, overlapping subjects</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Teacher Conflicts --}}
            @if(count($facultyConflicts) > 0)
            <h6 class="fw-bold text-danger mb-2"><i class="fas fa-user-times me-1"></i> Repeated Teacher Assignments</h6>
            <div style="max-height: 200px; overflow-y: auto;" class="mb-3">
                @foreach($facultyConflicts as $conflict)
                <div class="conflict-item">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-danger"><i class="fas fa-user-tie"></i> {{ $conflict['faculty']->first_name }} {{ $conflict['faculty']->last_name }}</span>
                        <span class="text-muted small">is teaching both</span>
                        <span class="badge bg-dark">{{ $conflict['slot_a']->subject->name ?? 'N/A' }}</span>
                        <span class="text-muted small">and</span>
                        <span class="badge bg-dark">{{ $conflict['slot_b']->subject->name ?? 'N/A' }}</span>
                        <span class="text-muted small">on</span>
                        <span class="fw-bold">{{ $conflict['slot_a']->day_of_week }}</span>
                        <span class="text-muted small">
                            {{ date('h:i A', strtotime($conflict['slot_a']->start_time)) }}-{{ date('h:i A', strtotime($conflict['slot_a']->end_time)) }}
                            ↔ {{ date('h:i A', strtotime($conflict['slot_b']->start_time)) }}-{{ date('h:i A', strtotime($conflict['slot_b']->end_time)) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Student Group Conflicts --}}
            @if(count($studentConflicts) > 0)
            <h6 class="fw-bold mb-2" style="color: #f57c00;"><i class="fas fa-users me-1"></i> Student Group Overlaps</h6>
            <div style="max-height: 200px; overflow-y: auto;">
                @foreach($studentConflicts as $conflict)
                <div class="conflict-item student-conflict">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge" style="background: #f57c00;"><i class="fas fa-graduation-cap"></i> {{ $conflict['course']->name ?? 'N/A' }} Sem {{ $conflict['semester'] }}</span>
                        <span class="text-muted small">has both</span>
                        <span class="badge bg-dark">{{ $conflict['slot_a']->subject->name ?? 'N/A' }}</span>
                        <span class="text-muted small">and</span>
                        <span class="badge bg-dark">{{ $conflict['slot_b']->subject->name ?? 'N/A' }}</span>
                        <span class="text-muted small">on</span>
                        <span class="fw-bold">{{ $conflict['slot_a']->day_of_week }}</span>
                        <span class="text-muted small">
                            {{ date('h:i A', strtotime($conflict['slot_a']->start_time)) }}-{{ date('h:i A', strtotime($conflict['slot_a']->end_time)) }}
                            ↔ {{ date('h:i A', strtotime($conflict['slot_b']->start_time)) }}-{{ date('h:i A', strtotime($conflict['slot_b']->end_time)) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endif

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
                    <div class="mb-3">
                        <label class="form-label">Room / Classroom</label>
                        <input type="text" name="room" class="form-control" placeholder="e.g. Room 101 (Optional)">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Slot</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Timetable List -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Timetable Slots</h6>
                @if(count($facultyConflicts) > 0 || count($studentConflicts) > 0)
                <div class="d-flex gap-1">
                    <button class="btn btn-sm filter-btn btn-outline-secondary active" onclick="filterTable('all', this)" id="filterAll">All</button>
                    <button class="btn btn-sm filter-btn btn-outline-danger" onclick="filterTable('conflicts', this)" id="filterConflicts">
                        <i class="fas fa-exclamation-triangle"></i> Conflicts Only
                    </button>
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable" width="100%" cellspacing="0" id="timetableTable">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Subject</th>
                                <th>Course/Sem</th>
                                <th>Faculty</th>
                                <th>Room</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($timetables as $slot)
                            @php
                                $isConflict = $conflictIds->contains($slot->id);
                                // Determine conflict type
                                $isFacultyConflict = false;
                                $isStudentConflict = false;
                                if ($isConflict) {
                                    foreach ($facultyConflicts as $fc) {
                                        if ($fc['slot_a']->id == $slot->id || $fc['slot_b']->id == $slot->id) {
                                            $isFacultyConflict = true;
                                            break;
                                        }
                                    }
                                    foreach ($studentConflicts as $sc) {
                                        if ($sc['slot_a']->id == $slot->id || $sc['slot_b']->id == $slot->id) {
                                            $isStudentConflict = true;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <tr class="{{ $isFacultyConflict ? 'row-conflict-teacher' : '' }} {{ $isStudentConflict && !$isFacultyConflict ? 'row-conflict-student' : '' }}" data-conflict="{{ $isConflict ? '1' : '0' }}">
                                <td><strong>{{ $slot->day_of_week }}</strong></td>
                                <td>{{ date('h:i A', strtotime($slot->start_time)) }} - {{ date('h:i A', strtotime($slot->end_time)) }}</td>
                                <td>{{ $slot->subject->name }} ({{ $slot->subject->code }})</td>
                                <td>{{ $slot->course->name }} (Sem {{ $slot->semester }})</td>
                                <td>
                                    {{ $slot->faculty->first_name }} {{ $slot->faculty->last_name }}
                                    @if($isFacultyConflict)
                                        <span class="badge bg-danger ms-1" title="Teacher has overlapping schedule"><i class="fas fa-exclamation-circle"></i></span>
                                    @endif
                                </td>
                                <td><span class="badge bg-light text-dark">{{ $slot->room ?? 'N/A' }}</span></td>
                                <td>
                                    @if($isFacultyConflict && $isStudentConflict)
                                        <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Teacher + Student Conflict</span>
                                    @elseif($isFacultyConflict)
                                        <span class="badge bg-danger"><i class="fas fa-user-times"></i> Teacher Conflict</span>
                                    @elseif($isStudentConflict)
                                        <span class="badge" style="background: #f57c00; color: #fff;"><i class="fas fa-users"></i> Student Overlap</span>
                                    @else
                                        <span class="badge bg-success"><i class="fas fa-check-circle"></i> OK</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <!-- Edit Trigger Button -->
                                        <button type="button" class="btn btn-sm btn-info text-white me-2" data-bs-toggle="modal" data-bs-target="#edit-modal-{{ $slot->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Delete Action -->
                                        <form action="{{ route('admin.timetable.delete', $slot->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade text-dark" id="edit-modal-{{ $slot->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $slot->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <form action="{{ route('admin.timetable.update', $slot->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold" id="editModalLabel-{{ $slot->id }}"><i class="fas fa-edit me-2 text-info"></i>Edit Timetable Slot</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Department</label>
                                                            <select name="department_id" class="form-control" required>
                                                                @foreach ($departments as $dept)
                                                                    <option value="{{ $dept->id }}" {{ $slot->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Course</label>
                                                            <select name="course_id" class="form-control" required>
                                                                @foreach ($courses as $course)
                                                                    <option value="{{ $course->id }}" {{ $slot->course_id == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Semester</label>
                                                            <input type="number" name="semester" class="form-control" min="1" max="10" required value="{{ $slot->semester }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Subject</label>
                                                            <select name="subject_id" class="form-control" required>
                                                                @foreach ($subjects as $sub)
                                                                    <option value="{{ $sub->id }}" {{ $slot->subject_id == $sub->id ? 'selected' : '' }}>{{ $sub->code }} - {{ $sub->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Faculty</label>
                                                            <select name="faculty_id" class="form-control" required>
                                                                @foreach ($facultyList as $fac)
                                                                    <option value="{{ $fac->id }}" {{ $slot->faculty_id == $fac->id ? 'selected' : '' }}>{{ $fac->first_name }} {{ $fac->last_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Day of Week</label>
                                                            <select name="day_of_week" class="form-control" required>
                                                                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                                                    <option value="{{ $day }}" {{ $slot->day_of_week == $day ? 'selected' : '' }}>{{ $day }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6 mb-3">
                                                                <label class="form-label">Start Time</label>
                                                                <input type="time" name="start_time" class="form-control" required value="{{ date('H:i', strtotime($slot->start_time)) }}">
                                                            </div>
                                                            <div class="col-6 mb-3">
                                                                <label class="form-label">End Time</label>
                                                                <input type="time" name="end_time" class="form-control" required value="{{ date('H:i', strtotime($slot->end_time)) }}">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Room / Classroom</label>
                                                            <input type="text" name="room" class="form-control" value="{{ $slot->room }}" placeholder="e.g. Room 101 (Optional)">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-info text-white">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
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
function filterTable(mode, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    var rows = document.querySelectorAll('#timetableTable tbody tr');
    rows.forEach(function(row) {
        if (mode === 'all') {
            row.style.display = '';
        } else if (mode === 'conflicts') {
            row.style.display = row.getAttribute('data-conflict') === '1' ? '' : 'none';
        }
    });
}
</script>
@endsection
