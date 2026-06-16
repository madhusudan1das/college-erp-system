@extends('layouts.app')

@section('title', 'Study Materials')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary-grad text-white">
        <h5 class="m-0 font-weight-bold"><i class="fas fa-folder-open me-2"></i>Study Materials & Notes</h5>
    </div>
    <div class="card-body">
        @if($materials->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fas fa-file-pdf fa-3x mb-3 text-secondary"></i>
                <p class="lead">No study materials have been uploaded for your subjects yet.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Description</th>
                            <th>Uploaded By</th>
                            <th>Uploaded At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materials as $material)
                            <tr>
                                <td class="fw-bold">{{ $material->title }}</td>
                                <td>{{ $material->subject->name }} ({{ $material->subject->code }})</td>
                                <td>{{ $material->description ?? 'N/A' }}</td>
                                <td>Prof. {{ $material->faculty->first_name }} {{ $material->faculty->last_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($material->created_at)->format('d M Y, h:i A') }}</td>
                                <td>
                                    <a href="{{ asset($material->file_path) }}" class="btn btn-sm btn-primary-grad" target="_blank">
                                        <i class="fas fa-download me-1"></i> Download
                                    </a>
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
