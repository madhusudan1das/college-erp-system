@extends('layouts.app')

@section('title', 'Faculty Attendance')

@section('styles')
<style>
    .btn-check {
        position: absolute;
        clip: rect(0,0,0,0);
        pointer-events: none;
    }
    .btn-group .btn {
        border-radius: 4px;
        margin: 0 2px;
        transition: all 0.2s ease;
    }
    .btn-check:checked + .btn-outline-success {
        background-color: #198754 !important;
        color: #fff !important;
        box-shadow: 0 4px 6px rgba(25,135,84,0.3);
    }
    .btn-check:checked + .btn-outline-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
        box-shadow: 0 4px 6px rgba(255,193,7,0.3);
    }
    .btn-check:checked + .btn-outline-danger {
        background-color: #dc3545 !important;
        color: #fff !important;
        box-shadow: 0 4px 6px rgba(220,53,69,0.3);
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Faculty Attendance System</h2>
</div>

<!-- Quick Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card bg-primary-grad shadow h-100 py-2">
            <div>
                <div class="text-xs font-weight-bold text-uppercase mb-1">Total Faculty</div>
                <div class="h3 mb-0 font-weight-bold">{{ $stats['total'] }}</div>
                <small class="text-white-50">Registered members</small>
            </div>
            <div class="icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card bg-success-grad shadow h-100 py-2">
            <div>
                <div class="text-xs font-weight-bold text-uppercase mb-1">Present Today</div>
                <div class="h3 mb-0 font-weight-bold">{{ $stats['present'] }}</div>
                <small class="text-white-50">
                    {{ $stats['total'] > 0 ? number_format(($stats['present'] / $stats['total']) * 100, 1) : 0 }}% attendance rate
                </small>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card bg-warning-grad shadow h-100 py-2">
            <div>
                <div class="text-xs font-weight-bold text-uppercase mb-1">Late Today</div>
                <div class="h3 mb-0 font-weight-bold">{{ $stats['late'] }}</div>
                <small class="text-white-50">
                    {{ $stats['total'] > 0 ? number_format(($stats['late'] / $stats['total']) * 100, 1) : 0 }}% of staff
                </small>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card bg-danger-grad shadow h-100 py-2">
            <div>
                <div class="text-xs font-weight-bold text-uppercase mb-1">Absent Today</div>
                <div class="h3 mb-0 font-weight-bold">{{ $stats['absent'] }}</div>
                <small class="text-white-50">
                    {{ $stats['total'] > 0 ? number_format(($stats['absent'] / $stats['total']) * 100, 1) : 0 }}% absent rate
                </small>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-light">
        <ul class="nav nav-tabs card-header-tabs" id="attendanceTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ $active_tab == 'mark' ? 'active font-weight-bold text-primary' : 'text-secondary' }}" 
                   href="?tab=mark&date={{ $selected_date }}">
                   <i class="fas fa-edit mr-1"></i> Mark Daily Attendance
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $active_tab == 'history' ? 'active font-weight-bold text-primary' : 'text-secondary' }}" 
                   href="?tab=history">
                   <i class="fas fa-history mr-1"></i> Attendance History Logs
                </a>
            </li>
        </ul>
    </div>
    
    <div class="card-body">
        @if ($active_tab == 'mark')
            <!-- MARK DAILY ATTENDANCE TAB -->
            <form method="GET" action="" class="row align-items-end mb-4 bg-light p-3 rounded shadow-sm">
                <input type="hidden" name="tab" value="mark">
                <div class="col-md-6 mb-2">
                    <label class="form-label font-weight-bold">Select Date for Attendance:</label>
                    <input type="date" name="date" class="form-control" value="{{ $selected_date }}" required max="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-6 mb-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sync"></i> Load Faculty List</button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.faculty-attendance.store') }}">
                @csrf
                <input type="hidden" name="date" value="{{ $selected_date }}">

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle" width="100%">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 30%;">Faculty Member</th>
                                <th style="width: 25%;">Department</th>
                                <th style="width: 20%;">Phone</th>
                                <th style="width: 25%;">Attendance Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($faculty_list->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center py-4">No faculty members found in the system.</td>
                                </tr>
                            @else
                                @foreach ($faculty_list as $faculty)
                                    @php
                                        $status = $attendanceMap->get($faculty->id, 'present');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-secondary text-white me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
                                                    {{ strtoupper(substr($faculty->first_name, 0, 1) . substr($faculty->last_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <span class="font-weight-bold d-block">{{ $faculty->first_name }} {{ $faculty->last_name }}</span>
                                                    <small class="text-muted">{{ $faculty->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary px-2 py-1">{{ $faculty->department->name }}</span>
                                        </td>
                                        <td>{{ $faculty->phone ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group w-100" role="group">
                                                <input type="radio" class="btn-check" name="attendance[{{ $faculty->id }}]" id="status_p_{{ $faculty->id }}" value="present" {{ $status == 'present' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success btn-sm" for="status_p_{{ $faculty->id }}"><i class="fas fa-check"></i> Present</label>

                                                <input type="radio" class="btn-check" name="attendance[{{ $faculty->id }}]" id="status_l_{{ $faculty->id }}" value="late" {{ $status == 'late' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-warning btn-sm" for="status_l_{{ $faculty->id }}"><i class="fas fa-clock"></i> Late</label>

                                                <input type="radio" class="btn-check" name="attendance[{{ $faculty->id }}]" id="status_a_{{ $faculty->id }}" value="absent" {{ $status == 'absent' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger btn-sm" for="status_a_{{ $faculty->id }}"><i class="fas fa-times"></i> Absent</label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success btn-lg px-5 shadow"><i class="fas fa-save"></i> Save Daily Attendance</button>
                </div>
            </form>
        @else
            <!-- ATTENDANCE HISTORY TAB -->
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Faculty Name</th>
                            <th>Department</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $row)
                            <tr>
                                <td data-sort="{{ strtotime($row->date) }}">
                                    {{ date('d M Y', strtotime($row->date)) }}
                                </td>
                                <td>{{ $row->faculty->first_name }} {{ $row->faculty->last_name }}</td>
                                <td>{{ $row->faculty->department->name }}</td>
                                <td>
                                    @if ($row->status == 'present')
                                        <span class="badge bg-success px-2 py-1"><i class="fas fa-check-circle"></i> Present</span>
                                    @elseif ($row->status == 'late')
                                        <span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-clock"></i> Late</span>
                                    @else
                                        <span class="badge bg-danger px-2 py-1"><i class="fas fa-times-circle"></i> Absent</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
