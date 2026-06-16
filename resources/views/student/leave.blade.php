@extends('layouts.app')

@section('title', 'Leave Application')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-paper-plane me-2"></i>Apply for Leave</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('student.leave.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Leave</label>
                        <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Explain the reason for leaving..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Supporting Document (Optional)</label>
                        <input type="file" class="form-control" id="document" name="document" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx">
                        <div class="form-text text-muted">Upload medical certificate or supporting document (Max 10MB)</div>
                    </div>
                    <button type="submit" class="btn btn-primary-grad w-100" style="background-color:aqua; color:black;><i class="fas fa-check me-1"></i> Submit Application</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-history me-2"></i>Leave Application History</h5>
            </div>
            <div class="card-body">
                @if($leaves->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-plane-departure fa-3x mb-3 text-secondary"></i>
                        <p class="lead">You haven't submitted any leave applications yet.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Reason</th>
                                    <th>Attachment</th>
                                    <th>Status</th>
                                    <th>Comments/Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaves as $leave)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>
                                        <td>{{ $leave->reason }}</td>
                                        <td>
                                            @if($leave->document_path)
                                                <a href="{{ asset($leave->document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-download"></i> View</a>
                                            @else
                                                <span class="text-muted small">None</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($leave->status === 'pending')
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending</span>
                                            @elseif($leave->status === 'approved')
                                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Approved</span>
                                            @else
                                                <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Rejected</span>
                                            @endif
                                        </td>
                                        <td>{{ $leave->comments ?? 'No remarks yet.' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
