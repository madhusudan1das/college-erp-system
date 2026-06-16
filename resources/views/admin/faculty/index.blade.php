@extends('layouts.app')

@section('title', 'Manage Faculty')

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
        background: linear-gradient(135deg, #2af598 0%, #009efd 100%);
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Faculty</h2>
    <a href="{{ route('admin.faculty.create') }}" class="btn btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Add New Faculty</a>
</div>

<!-- Filter Section -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-light border-bottom">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Filter Faculty</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.faculty') }}" class="row g-3 align-items-end">
            <div class="col-md-9">
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
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Filter</button>
                <a href="{{ route('admin.faculty') }}" class="btn btn-secondary w-100"><i class="fas fa-undo me-1"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Faculty List Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Faculty List</h6>
        <span class="badge bg-success rounded-pill">{{ count($facultyList) }} Active</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover datatable" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Phone</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($facultyList as $faculty)
                    <tr class="clickable-row"
                        data-first-name="{{ $faculty->first_name }}"
                        data-last-name="{{ $faculty->last_name }}"
                        data-email="{{ $faculty->user->email ?? 'N/A' }}"
                        data-username="{{ $faculty->user->username ?? 'N/A' }}"
                        data-department="{{ $faculty->department->name ?? 'N/A' }}"
                        data-phone="{{ $faculty->phone ?? 'Not Provided' }}"
                        data-address="{{ $faculty->address ?? 'Not Provided' }}">
                        <td class="fw-semibold">{{ $faculty->first_name }} {{ $faculty->last_name }}</td>
                        <td>{{ $faculty->user->email ?? 'N/A' }}</td>
                        <td><span class="badge bg-light text-primary border px-2.5 py-1.5">{{ $faculty->department->name ?? 'N/A' }}</span></td>
                        <td>{{ $faculty->phone ?? '-' }}</td>
                        <td onclick="event.stopPropagation();">
                            <a href="{{ route('admin.faculty.edit', $faculty->id) }}" class="btn btn-sm btn-info text-white" title="Edit Faculty"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.faculty.delete', $faculty->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this faculty member?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete Faculty"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Faculty Details Modal -->
<div class="modal fade" id="facultyDetailsModal" tabindex="-1" aria-labelledby="facultyDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold" id="facultyDetailsModalLabel"><i class="fas fa-chalkboard-teacher me-2"></i>Faculty Member Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Profile Card Header (Left) -->
                    <div class="col-md-4 bg-light text-center p-4 d-flex flex-column align-items-center justify-content-center border-end">
                        <div class="mb-3">
                            <div id="detail-profile-initial" class="rounded-circle text-white d-flex align-items-center justify-content-center shadow-sm mb-2" style="width: 120px; height: 120px; font-size: 3.5rem; font-weight: 700;">
                                F
                            </div>
                        </div>
                        <h5 id="detail-full-name" class="fw-bold text-dark mb-1">Jane Doe</h5>
                        <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm mb-2" id="detail-dept-badge">Department</span>
                        <p class="text-muted small mb-0"><i class="fas fa-graduation-cap me-1"></i> Faculty Profile</p>
                    </div>
                    <!-- Detailed Info (Right) -->
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
                                <label class="text-uppercase small fw-bold text-muted d-block mb-1">Department</label>
                                <span id="detail-department" class="text-dark fw-semibold">-</span>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-uppercase small fw-bold text-muted d-block mb-1">Phone Number</label>
                                <span id="detail-phone" class="text-dark fw-semibold">-</span>
                            </div>

                            <hr class="my-2 text-muted opacity-25">

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
        const email = row.data('email');
        const username = row.data('username');
        const department = row.data('department');
        const phone = row.data('phone');
        const address = row.data('address');

        // Populate modal text
        $('#detail-full-name').text(firstName + ' ' + lastName);
        $('#detail-dept-badge').text(department);
        $('#detail-username').text(username);
        $('#detail-email').text(email);
        $('#detail-department').text(department);
        $('#detail-phone').text(phone);
        $('#detail-address').text(address);

        // Profile initial letter
        $('#detail-profile-initial').text(firstName.charAt(0).toUpperCase());

        // Show modal
        $('#facultyDetailsModal').modal('show');
    });
});
</script>
@endsection
