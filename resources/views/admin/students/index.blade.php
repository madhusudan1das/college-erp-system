@extends('layouts.app')

@section('title', 'Manage Students')

@section('styles')
<style>
    .clickable-row {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .clickable-row:hover {
        background-color: rgba(99, 102, 241, 0.08) !important;
        transform: translateY(-1px);
        box-shadow: inset 0 0 0 1px rgba(99, 102, 241, 0.15);
    }
    #detail-profile-initial {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Students</h2>
    <a href="{{ route('admin.students.create') }}" class="btn btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Add New Student</a>
</div>

<!-- Filter Section -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-light border-bottom">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Filter Students</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.students') }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="department_id" class="form-label small fw-bold text-muted text-uppercase">Department</label>
                <select name="department_id" id="department_id" class="form-select">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }} ({{ $dept->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="semester" class="form-label small fw-bold text-muted text-uppercase">Semester</label>
                <select name="semester" id="semester" class="form-select">
                    <option value="">All Semesters</option>
                    @for($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>
                            Semester {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Filter</button>
                <a href="{{ route('admin.students') }}" class="btn btn-secondary w-100"><i class="fas fa-undo me-1"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Student List Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Student List</h6>
        <span class="badge bg-primary rounded-pill">{{ count($students) }} Registered</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover datatable" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Enrollment No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Semester</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                    <tr class="clickable-row" 
                        data-first-name="{{ $student->first_name }}"
                        data-last-name="{{ $student->last_name }}"
                        data-enrollment-no="{{ $student->enrollment_no }}"
                        data-email="{{ $student->user->email ?? 'N/A' }}"
                        data-username="{{ $student->user->username ?? 'N/A' }}"
                        data-course="{{ $student->course->name ?? 'N/A' }}"
                        data-department="{{ $student->course->department->name ?? 'N/A' }}"
                        data-semester="{{ $student->current_semester }}"
                        data-phone="{{ $student->phone ?? 'Not Provided' }}"
                        data-address="{{ $student->address ?? 'Not Provided' }}"
                        data-dob="{{ $student->dob ?? 'Not Provided' }}"
                        data-profile-image="{{ $student->profile_image ? asset($student->profile_image) : '' }}">
                        <td class="fw-bold text-primary">{{ $student->enrollment_no }}</td>
                        <td class="fw-semibold">{{ $student->first_name }} {{ $student->last_name }}</td>
                        <td>{{ $student->user->email ?? 'N/A' }}</td>
                        <td>{{ $student->course->name ?? 'N/A' }}</td>
                        <td class="text-center"><span class="badge bg-secondary px-2.5 py-1.5">Sem {{ $student->current_semester }}</span></td>
                        <td onclick="event.stopPropagation();">
                            <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-sm btn-info text-white" title="Edit Student"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.students.delete', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete Student"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Student Details Modal -->
<div class="modal fade" id="studentDetailsModal" tabindex="-1" aria-labelledby="studentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="studentDetailsModalLabel"><i class="fas fa-user-graduate me-2"></i>Student Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Profile Header Card (Left Column) -->
                    <div class="col-md-4 bg-light text-center p-4 d-flex flex-column align-items-center justify-content-center border-end">
                        <div class="mb-3 position-relative">
                            <img id="detail-profile-img" src="" alt="Profile Image" class="rounded-circle img-thumbnail shadow-sm mb-2" style="width: 120px; height: 120px; object-fit: cover; display: none;">
                            <div id="detail-profile-initial" class="rounded-circle text-white d-flex align-items-center justify-content-center shadow-sm mb-2" style="width: 120px; height: 120px; font-size: 3.5rem; font-weight: 700;">
                                S
                            </div>
                        </div>
                        <h5 id="detail-full-name" class="fw-bold text-dark mb-1">John Doe</h5>
                        <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm mb-2 font-monospace" id="detail-enrollment-badge">Enrollment No</span>
                        <p class="text-muted small mb-0"><i class="fas fa-id-card me-1"></i> Student Record</p>
                    </div>
                    <!-- Detailed Info (Right Column) -->
                    <div class="col-md-8 p-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="text-uppercase small fw-bold text-muted d-block mb-1">Username</label>
                                <span id="detail-username" class="text-dark fw-semibold">-</span>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-uppercase small fw-bold text-muted d-block mb-1">Email Address</label>
                                <span id="detail-email" class="text-dark fw-semibold">-</span>
                            </div>
                            
                            <hr class="my-2 text-muted opacity-25">

                            <div class="col-sm-6">
                                <label class="text-uppercase small fw-bold text-muted d-block mb-1">Course</label>
                                <span id="detail-course" class="text-dark fw-semibold">-</span>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-uppercase small fw-bold text-muted d-block mb-1">Department</label>
                                <span id="detail-department" class="text-dark fw-semibold">-</span>
                            </div>

                            <div class="col-sm-6">
                                <label class="text-uppercase small fw-bold text-muted d-block mb-1">Current Semester</label>
                                <span id="detail-semester" class="badge bg-secondary px-2.5 py-1.5 fw-semibold">-</span>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-uppercase small fw-bold text-muted d-block mb-1">Date of Birth</label>
                                <span id="detail-dob" class="text-dark fw-semibold">-</span>
                            </div>

                            <hr class="my-2 text-muted opacity-25">

                            <div class="col-sm-6">
                                <label class="text-uppercase small fw-bold text-muted d-block mb-1">Phone Number</label>
                                <span id="detail-phone" class="text-dark fw-semibold">-</span>
                            </div>
                            <div class="col-12">
                                <label class="text-uppercase small fw-bold text-muted d-block mb-1">Residential Address</label>
                                <span id="detail-address" class="text-dark fw-semibold d-block">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0 py-2.5">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Row click handler to show details modal (excluding the Actions cell)
    $('.clickable-row td:not(:last-child)').on('click', function() {
        const row = $(this).parent('.clickable-row');
        
        const firstName = row.data('first-name');
        const lastName = row.data('last-name');
        const enrollment = row.data('enrollment-no');
        const email = row.data('email');
        const username = row.data('username');
        const course = row.data('course');
        const department = row.data('department');
        const semester = row.data('semester');
        const phone = row.data('phone');
        const address = row.data('address');
        const dob = row.data('dob');
        const profileImg = row.data('profile-image');

        // Populate modal text
        $('#detail-full-name').text(firstName + ' ' + lastName);
        $('#detail-enrollment-badge').text(enrollment);
        $('#detail-username').text(username);
        $('#detail-email').text(email);
        $('#detail-course').text(course);
        $('#detail-department').text(department);
        $('#detail-semester').text('Semester ' + semester);
        $('#detail-dob').text(dob);
        $('#detail-phone').text(phone);
        $('#detail-address').text(address);

        // Profile image handling
        if (profileImg) {
            $('#detail-profile-img').attr('src', profileImg).show();
            $('#detail-profile-initial').hide();
        } else {
            $('#detail-profile-img').hide();
            // Show initial letter of student
            $('#detail-profile-initial').text(firstName.charAt(0).toUpperCase()).show();
        }

        // Show modal
        $('#studentDetailsModal').modal('show');
    });
});
</script>
@endsection
