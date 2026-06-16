@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">My Attendance</h2>
</div>

<!-- Attendance Stats -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card bg-primary-grad shadow h-100 py-2">
            <div>
                <div class="text-xs font-weight-bold text-uppercase mb-1">Total Classes</div>
                <div class="h3 mb-0 font-weight-bold">{{ $stats['total'] }}</div>
            </div>
            <div class="icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card bg-success-grad shadow h-100 py-2">
            <div>
                <div class="text-xs font-weight-bold text-uppercase mb-1">Present Classes</div>
                <div class="h3 mb-0 font-weight-bold">{{ $stats['present'] }}</div>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card bg-warning-grad shadow h-100 py-2">
            <div>
                <div class="text-xs font-weight-bold text-uppercase mb-1">Late Classes</div>
                <div class="h3 mb-0 font-weight-bold">{{ $stats['late'] }}</div>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card bg-danger-grad shadow h-100 py-2">
            <div>
                <div class="text-xs font-weight-bold text-uppercase mb-1">Attendance Rate</div>
                <div class="h3 mb-0 font-weight-bold">
                    {{ $stats['total'] > 0 ? number_format((($stats['present'] + ($stats['late'] * 0.5)) / $stats['total']) * 100, 1) : 0 }}%
                </div>
            </div>
            <div class="icon">
                <i class="fas fa-percentage"></i>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-light">
        <h6 class="m-0 font-weight-bold text-primary">Attendance History Logs</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attendance as $row)
                    <tr>
                        <td data-sort="{{ strtotime($row->date) }}">{{ date('d M Y', strtotime($row->date)) }}</td>
                        <td><span class="badge bg-dark">{{ $row->subject->code }}</span></td>
                        <td>{{ $row->subject->name }}</td>
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
    </div>
</div>
@endsection
