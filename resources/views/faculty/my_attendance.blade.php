@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">My Attendance Logs</h2>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Attendance History</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attendance as $record)
                    <tr>
                        <td data-sort="{{ strtotime($record->date) }}">{{ date('d M Y', strtotime($record->date)) }}</td>
                        <td>
                            @if ($record->status == 'present')
                                <span class="badge bg-success px-2 py-1"><i class="fas fa-check-circle"></i> Present</span>
                            @elseif ($record->status == 'late')
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
