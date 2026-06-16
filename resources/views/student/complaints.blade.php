@extends('layouts.app')

@section('title', 'Complaints & Grievances')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-exclamation-circle me-2"></i>File a Grievance</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('student.complaints.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Title / Subject</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Brief subject of the complaint..." required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Detailed Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5" placeholder="Explain the issue in detail..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary-grad w-100"><i class="fas fa-paper-plane me-1"></i> Submit Grievance</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-list me-2"></i>My Filed Grievances</h5>
            </div>
            <div class="card-body">
                @if($complaints->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-check-double fa-3x mb-3 text-success"></i>
                        <p class="lead">All clear! You haven't filed any grievances.</p>
                    </div>
                @else
                    <div class="list-group">
                        @foreach($complaints as $complaint)
                            <div class="list-group-item list-group-item-action flex-column align-items-start border-start border-4 border-{{ $complaint->status === 'resolved' ? 'success' : 'warning' }} mb-3 rounded shadow-sm">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h5 class="mb-1 text-primary fw-bold">{{ $complaint->title }}</h5>
                                    <div>
                                        @if($complaint->status === 'pending')
                                            <span class="badge bg-warning text-dark"><i class="fas fa-spinner fa-spin me-1"></i> Pending</span>
                                        @else
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Resolved</span>
                                        @endif
                                    </div>
                                </div>
                                <p class="mb-2 text-dark mt-2">{{ $complaint->description }}</p>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><i class="fas fa-clock me-1"></i> Filed At: {{ \Carbon\Carbon::parse($complaint->created_at)->format('d M Y, h:i A') }}</small>
                                </div>
                                @if($complaint->status === 'resolved')
                                    <div class="bg-light p-3 rounded mt-2 border-start border-4 border-success">
                                        <strong class="text-success"><i class="fas fa-comment-dots me-1"></i> Resolution Action:</strong>
                                        <p class="mb-0 text-muted mt-1">{{ $complaint->resolution }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
