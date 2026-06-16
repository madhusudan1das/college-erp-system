@extends('layouts.app')

@section('title', 'Manage Notices')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Notice Board</h2>
</div>

<div class="row">
    <!-- Publish Notice Form -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Publish New Notice</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.notices.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Notice Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Exam Schedule Announcement">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" class="form-control" rows="5" required placeholder="Write notice details..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Audience</label>
                        <select name="role_id" class="form-control">
                            <option value="">All Users (Students & Faculty)</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ ucfirst($role->name) }} Only</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attachment (Optional)</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Publish Notice</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Notice List -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Notices</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Target Role</th>
                                <th>Creator</th>
                                <th>Attachment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notices as $notice)
                            <tr>
                                <td data-sort="{{ strtotime($notice->created_at) }}">
                                    {{ $notice->created_at->format('d M Y h:i A') }}
                                </td>
                                <td>
                                    <strong>{{ $notice->title }}</strong>
                                    <p class="text-muted small mb-0">{{ Str::limit($notice->content, 100) }}</p>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $notice->role ? 'info' : 'secondary' }}">
                                        {{ $notice->role ? ucfirst($notice->role->name) : 'All' }}
                                    </span>
                                </td>
                                <td>{{ $notice->creator->username }}</td>
                                <td>
                                    @if ($notice->attachment)
                                        <a href="{{ asset($notice->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-paperclip"></i> View</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.notices.delete', $notice->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this notice?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
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
