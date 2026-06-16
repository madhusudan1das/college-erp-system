@extends('layouts.app')

@section('title', 'Manage Assignments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Assignments Management</h2>
</div>

<div class="row">
    <!-- Publish Assignment Form -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Publish New Assignment</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('faculty.assignments.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Assignment Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Lab Report 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Assignment details..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <select name="subject_id" class="form-control" required>
                            <option value="">Select Subject</option>
                            @foreach ($subjects as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deadline</label>
                        <input type="datetime-local" name="deadline" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attachment (Optional)</label>
                        <input type="file" name="file" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Publish Assignment</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Assignment List -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Published Assignments</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Title</th>
                                <th>Deadline</th>
                                <th>Attachment</th>
                                <th>Submissions</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assignments as $asn)
                            <tr>
                                <td>{{ $asn->subject->name }}</td>
                                <td>
                                    <strong>{{ $asn->title }}</strong>
                                    @if ($asn->description)
                                        <p class="text-muted small mb-0">{{ Str::limit($asn->description, 100) }}</p>
                                    @endif
                                </td>
                                <td>{{ $asn->deadline->format('d M Y h:i A') }}</td>
                                <td>
                                    @if ($asn->file_path)
                                        <a href="{{ asset($asn->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-download"></i> File</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('faculty.assignments.submissions', $asn->id) }}" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-folder-open"></i> Submissions ({{ $asn->submissions()->count() }})
                                    </a>
                                </td>
                                <td>
                                    <form action="{{ route('faculty.assignments.delete', $asn->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this assignment?');">
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
