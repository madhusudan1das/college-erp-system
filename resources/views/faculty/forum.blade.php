@extends('layouts.app')

@section('title', 'Discussion Forum')

@section('content')
<div class="row">
    <!-- Topics List -->
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary-grad text-white d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-comments me-2"></i>Campus Discussion Forum</h5>
            </div>
            <div class="card-body">
                @if($topics->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-comments fa-3x mb-3 text-secondary"></i>
                        <p class="lead">No discussions have been started yet. Be the first to start one!</p>
                    </div>
                @else
                    @foreach($topics as $topic)
                        <div class="card border-left-primary mb-3 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="card-title text-primary fw-bold mb-0">{{ $topic->title }}</h5>
                                    <small class="text-muted"><i class="fas fa-clock me-1"></i> {{ \Carbon\Carbon::parse($topic->created_at)->diffForHumans() }}</small>
                                </div>
                                <p class="card-text text-dark">{{ $topic->content }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-secondary fw-semibold">
                                        <i class="fas fa-user-circle me-1"></i> Posted by: {{ $topic->user->username }} 
                                        <span class="badge bg-secondary ms-1">{{ $topic->user->role->name }}</span>
                                    </small>
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#replies-{{ $topic->id }}">
                                        <i class="fas fa-reply-all me-1"></i> Replies ({{ $topic->replies->count() }})
                                    </button>
                                </div>

                                <!-- Replies Collapse Area -->
                                <div class="collapse mt-3" id="replies-{{ $topic->id }}">
                                    <div class="bg-light p-3 rounded">
                                        <h6 class="border-bottom pb-2 mb-2 text-dark font-weight-bold">Discussion Replies</h6>
                                        
                                        @if($topic->replies->isEmpty())
                                            <p class="text-muted small py-2 mb-0">No replies yet. Share your thoughts below!</p>
                                        @else
                                            <div class="mb-3">
                                                @foreach($topic->replies as $reply)
                                                    <div class="bg-white p-2 rounded shadow-sm mb-2 border-start border-4 border-info">
                                                        <div class="d-flex justify-content-between">
                                                            <small class="text-primary fw-bold">
                                                                {{ $reply->user->username }} 
                                                                <span class="badge bg-light text-dark border ms-1">{{ $reply->user->role->name }}</span>
                                                            </small>
                                                            <small class="text-muted">{{ \Carbon\Carbon::parse($reply->created_at)->diffForHumans() }}</small>
                                                        </div>
                                                        <p class="mb-0 text-dark small mt-1">{{ $reply->reply_text }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Post Reply Form -->
                                        <form action="{{ route('faculty.forum.reply.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="topic_id" value="{{ $topic->id }}">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="reply_text" placeholder="Write a reply..." required>
                                                <button class="btn btn-primary-grad" type="submit"><i class="fas fa-paper-plane"></i></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Create Topic Form -->
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-plus-circle me-2"></i>Start a Discussion</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('faculty.forum.topic.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Topic Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="What is your discussion topic about?" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content / Details</label>
                        <textarea class="form-control" id="content" name="content" rows="6" placeholder="Explain the discussion details..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success-grad w-100"><i class="fas fa-check-circle me-1"></i> Post Topic</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
