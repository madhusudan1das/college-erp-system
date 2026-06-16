@extends('layouts.app')

@section('title', 'Complaints & Grievances')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary-grad text-white">
        <h5 class="m-0 font-weight-bold"><i class="fas fa-exclamation-circle me-2"></i>Complaints & Grievances Manager</h5>
    </div>
    <div class="card-body">
        @if($complaints->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fas fa-check-double fa-3x mb-3 text-success"></i>
                <p class="lead">All clear! No pending grievances are recorded.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Role</th>
                            <th>Grievance Title</th>
                            <th>Description</th>
                            <th>Filed At</th>
                            <th>Status</th>
                            <th>Action / Resolution</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complaints as $complaint)
                            <tr>
                                <td class="fw-bold">
                                    @if($complaint->user->role->name === 'student')
                                        {{ $complaint->user->student->first_name ?? 'N/A' }} {{ $complaint->user->student->last_name ?? '' }}
                                    @elseif($complaint->user->role->name === 'faculty')
                                        Prof. {{ $complaint->user->faculty->first_name ?? 'N/A' }} {{ $complaint->user->faculty->last_name ?? '' }}
                                    @else
                                        {{ $complaint->user->username }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $complaint->user->role->name === 'student' ? 'info' : 'secondary' }}">
                                        {{ ucfirst($complaint->user->role->name) }}
                                    </span>
                                </td>
                                <td class="text-primary fw-semibold">{{ $complaint->title }}</td>
                                <td>{{ $complaint->description }}</td>
                                <td>{{ \Carbon\Carbon::parse($complaint->created_at)->format('d M Y, h:i A') }}</td>
                                <td>
                                    @if($complaint->status === 'pending')
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Pending</span>
                                    @else
                                        <span class="badge bg-success"><i class="fas fa-check-circle text-white"></i> Resolved</span>
                                    @endif
                                </td>
                                <td>
                                    @if($complaint->status === 'pending')
                                        <button type="button" class="btn btn-sm btn-primary-grad" data-bs-toggle="modal" data-bs-target="#resolve-modal-{{ $complaint->id }}">
                                            <i class="fas fa-gavel"></i> Resolve
                                        </button>

                                        <!-- Resolve Modal -->
                                        <div class="modal fade text-dark" id="resolve-modal-{{ $complaint->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.complaints.resolve', $complaint->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Provide Resolution</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Resolution Actions Taken</label>
                                                                <textarea class="form-control" name="resolution" rows="4" placeholder="Explain how this complaint was resolved..." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary-grad text-white">Submit Resolution</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-success small fw-semibold"><i class="fas fa-check text-success"></i> Resolved:</span>
                                        <p class="mb-0 text-muted small mt-1">{{ $complaint->resolution }}</p>
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
