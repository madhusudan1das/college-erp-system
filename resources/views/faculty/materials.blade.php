@extends('layouts.app')

@section('title', 'Study Materials Upload')

@section('content')
<div class="row">
    <!-- Upload Material Form -->
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-upload me-2"></i>Upload Study Material</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('faculty.materials.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Material Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="e.g. Lecture 1 Notes, Syllabus..." required>
                    </div>
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select class="form-select" id="subject_id" name="subject_id" required>
                            <option value="">-- Select Subject --</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief description of file contents..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">Choose File (PDF, DOCX, PPTX, ZIP - Max 20MB)</label>
                        <input class="form-control" type="file" id="file" name="file" required>
                    </div>
                    <button type="submit" class="btn btn-primary-grad w-100"><i class="fas fa-cloud-upload-alt me-1"></i> Upload File</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Uploaded Materials List -->
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-list me-2"></i>My Uploaded Materials</h5>
            </div>
            <div class="card-body">
                @if($materials->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fa-3x mb-3 text-secondary"></i>
                        <p class="lead">You haven't uploaded any study materials yet.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Subject</th>
                                    <th>Description</th>
                                    <th>Uploaded At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($materials as $material)
                                    <tr>
                                        <td class="fw-bold">{{ $material->title }}</td>
                                        <td>{{ $material->subject->name }}</td>
                                        <td>{{ $material->description ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($material->created_at)->format('d M Y, h:i A') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ asset($material->file_path) }}" class="btn btn-sm btn-info text-white" target="_blank" title="View/Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <form action="{{ route('faculty.materials.delete', $material->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this study material?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
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
