@extends('layouts.app')

@section('title', 'Research Publications & Records')

@section('content')
<div class="row">
    <!-- Log Publication Form -->
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-graduation-cap me-2"></i>Record Publication / Research</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('faculty.research.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Research Paper Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="e.g. A study on neural network heuristics..." required>
                    </div>
                    <div class="mb-3">
                        <label for="journal_name" class="form-label">Journal / Conference Name</label>
                        <input type="text" class="form-control" id="journal_name" name="journal_name" placeholder="e.g. IEEE Transactions, Springer..." required>
                    </div>
                    <div class="mb-3">
                        <label for="publication_date" class="form-label">Publication Date</label>
                        <input type="date" class="form-control" id="publication_date" name="publication_date" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Abstract / Short Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Brief summary of research findings..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">Attach Paper File (PDF, Optional - Max 10MB)</label>
                        <input class="form-control" type="file" id="file" name="file">
                    </div>
                    <button type="submit" class="btn btn-primary-grad w-100"><i class="fas fa-check-circle me-1"></i> Save Publication Record</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Publications List -->
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-journal-whills me-2"></i>My Research Publications</h5>
            </div>
            <div class="card-body">
                @if($publications->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-book-reader fa-3x mb-3 text-secondary"></i>
                        <p class="lead">No research publication records logged yet.</p>
                    </div>
                @else
                    <div class="list-group">
                        @foreach($publications as $pub)
                            <div class="list-group-item list-group-item-action flex-column align-items-start border-start border-4 border-success mb-3 rounded shadow-sm">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h5 class="mb-1 text-success fw-bold">{{ $pub->title }}</h5>
                                    <span class="badge bg-light text-dark border"><i class="fas fa-calendar-alt me-1 text-success"></i> Published: {{ \Carbon\Carbon::parse($pub->publication_date)->format('d M Y') }}</span>
                                </div>
                                <h6 class="text-muted small mt-1"><i class="fas fa-book me-1"></i> Journal: {{ $pub->journal_name }}</h6>
                                <p class="mb-2 text-dark small mt-2">{{ $pub->description ?? 'No abstract provided.' }}</p>
                                
                                @if($pub->file_path)
                                    <div class="mt-2">
                                        <a href="{{ asset($pub->file_path) }}" class="btn btn-sm btn-outline-success" target="_blank">
                                            <i class="fas fa-file-pdf me-1"></i> View Attached Paper
                                        </a>
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
