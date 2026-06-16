@extends('layouts.app')

@section('title', 'Leave Requests Manager')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary-grad text-white">
        <h5 class="m-0 font-weight-bold"><i class="fas fa-plane-departure me-2"></i>Leave Applications Manager</h5>
    </div>
    <div class="card-body">
        @if($leaves->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                <p class="lead">No leave applications currently registered.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Role</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Reason</th>
                            <th>Attachment</th>
                            <th>Status</th>
                            <th>Action / Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaves as $leave)
                            <tr>
                                <td class="fw-bold">
                                    @if($leave->user->role->name === 'student')
                                        {{ $leave->user->student->first_name ?? 'N/A' }} {{ $leave->user->student->last_name ?? '' }} (Student)
                                    @elseif($leave->user->role->name === 'faculty')
                                        Prof. {{ $leave->user->faculty->first_name ?? 'N/A' }} {{ $leave->user->faculty->last_name ?? '' }} (Faculty)
                                    @else
                                        {{ $leave->user->username }} (Admin)
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $leave->user->role->name === 'student' ? 'info' : 'secondary' }}">
                                        {{ ucfirst($leave->user->role->name) }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>
                                <td>{{ $leave->reason }}</td>
                                <td>
                                    @if($leave->document_path)
                                        <a href="{{ asset($leave->document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-download"></i> View</a>
                                    @else
                                        <span class="text-slate small text-muted">None</span>
                                    @endif
                                </td>
                                <td>
                                    @if($leave->status === 'pending')
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Pending</span>
                                    @elseif($leave->status === 'approved')
                                        <span class="badge bg-success"><i class="fas fa-check-circle text-white"></i> Approved</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fas fa-times-circle text-white"></i> Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    @if($leave->status === 'pending')
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-success text-white" data-bs-toggle="modal" data-bs-target="#approve-modal-{{ $leave->id }}">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger text-white" data-bs-toggle="modal" data-bs-target="#reject-modal-{{ $leave->id }}">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </div>

                                        <!-- Approve Modal -->
                                        <div class="modal fade text-dark" id="approve-modal-{{ $leave->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.leaves.approve', $leave->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Approve Leave Application</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Approval Comments / Remarks</label>
                                                                <textarea class="form-control" name="comments" rows="3" placeholder="Approval message..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-success text-white">Approve Leave</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade text-dark" id="reject-modal-{{ $leave->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.leaves.reject', $leave->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Reject Leave Application</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Rejection Reason / Comments</label>
                                                                <textarea class="form-control" name="comments" rows="3" placeholder="Rejection message..." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-danger text-white">Reject Leave</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">Processed: {{ $leave->comments ?? 'No comments' }}</span>
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
