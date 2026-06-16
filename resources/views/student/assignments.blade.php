@extends('layouts.app')

@section('title', 'My Assignments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">My Assignments</h2>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-light">
        <h6 class="m-0 font-weight-bold text-primary">Class Assignments</h6>
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
                        <th>My Submission</th>
                        <th>Submission Form / Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assignments as $asn)
                        @php
                            $submission = $asn->submissions->first();
                            $isOverdue = now()->gt($asn->deadline);
                        @endphp
                        <tr>
                            <td>{{ $asn->subject->name }} ({{ $asn->subject->code }})</td>
                            <td>
                                <strong>{{ $asn->title }}</strong>
                                @if ($asn->description)
                                    <p class="text-muted small mb-0">{{ $asn->description }}</p>
                                @endif
                            </td>
                            <td>
                                <span class="{{ $isOverdue && !$submission ? 'text-danger fw-bold' : '' }}">
                                    {{ $asn->deadline->format('d M Y h:i A') }}
                                </span>
                            </td>
                            <td>
                                @if ($asn->file_path)
                                    <a href="{{ asset($asn->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-download"></i> File</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($submission)
                                    <a href="{{ asset($submission->file_path) }}" target="_blank" class="btn btn-sm btn-outline-success"><i class="fas fa-eye"></i> View Upload</a>
                                @else
                                    <span class="text-muted small fst-italic">Not submitted</span>
                                @endif
                            </td>
                            <td>
                                @if ($submission)
                                    @if ($submission->status == 'graded')
                                        <span class="badge bg-success"><i class="fas fa-check-circle"></i> Graded</span>
                                    @elseif ($submission->status == 'late')
                                        <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i> Submitted Late</span>
                                    @else
                                        <span class="badge bg-primary"><i class="fas fa-spinner"></i> Submitted</span>
                                    @endif
                                @else
                                    <form action="{{ route('student.assignments.submit', $asn->id) }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-center">
                                        @csrf
                                        <div class="col-auto">
                                            <input type="file" name="file" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-sm btn-success">Upload</button>
                                        </div>
                                    </form>
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
