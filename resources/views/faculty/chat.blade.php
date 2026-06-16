@extends('layouts.app')

@section('title', 'Direct Messaging')

@section('content')
<div class="row" style="height: calc(100vh - 160px);">
    <!-- Users Directory Pane -->
    <div class="col-md-4 h-100 d-flex flex-column mb-4">
        <div class="card shadow h-100 mb-0">
            <div class="card-header bg-primary-grad text-white py-3">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-users me-2"></i>My Students Directory</h5>
            </div>
            <div class="card-body overflow-auto p-0" style="max-height: 500px;">
                <div class="list-group list-group-flush">
                    @foreach($students as $stud)
                        <a href="{{ route('faculty.chat', ['user_id' => $stud->user->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center py-3 {{ $selectedUser && $selectedUser->id == $stud->user->id ? 'active bg-light text-dark font-weight-bold' : '' }}">
                            <div class="bg-success text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold">{{ $stud->first_name }} {{ $stud->last_name }}</h6>
                                </div>
                                <span class="small text-muted">{{ $stud->course->name }} | Sem {{ $stud->current_semester }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Conversation Pane -->
    <div class="col-md-8 h-100 d-flex flex-column">
        <div class="card shadow h-100 d-flex flex-column mb-0">
            @if($selectedUser)
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-info text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">{{ $selectedUser->username }}</h5>
                            <span class="text-success small"><i class="fas fa-circle fa-xs me-1"></i> Active Chat</span>
                        </div>
                    </div>
                </div>

                <div class="card-body overflow-auto bg-light p-4 flex-grow-1" style="height: 350px;" id="chat-messages-container">
                    @if($messages->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="far fa-comments fa-3x mb-3 text-secondary"></i>
                            <p class="mb-0">No past conversation found. Say hello!</p>
                        </div>
                    @else
                        @foreach($messages as $msg)
                            @if($msg->sender_id == Auth::id())
                                <!-- Outgoing Message -->
                                <div class="d-flex justify-content-end mb-3">
                                    <div class="bg-primary text-white p-3 rounded shadow-sm text-end" style="max-width: 75%; border-radius: 15px 15px 0 15px !important;">
                                        <p class="mb-1 text-start">{{ $msg->message_text }}</p>
                                        <small style="font-size: 0.75rem; opacity: 0.8;"><i class="fas fa-clock me-1"></i> {{ \Carbon\Carbon::parse($msg->created_at)->format('h:i A') }}</small>
                                    </div>
                                </div>
                            @else
                                <!-- Incoming Message -->
                                <div class="d-flex justify-content-start mb-3">
                                    <div class="bg-white text-dark p-3 rounded shadow-sm" style="max-width: 75%; border-radius: 15px 15px 15px 0 !important; border-left: 4px solid #17a2b8;">
                                        <p class="mb-1">{{ $msg->message_text }}</p>
                                        <small class="text-muted" style="font-size: 0.75rem;"><i class="fas fa-clock me-1"></i> {{ \Carbon\Carbon::parse($msg->created_at)->format('h:i A') }}</small>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                <div class="card-footer bg-white border-top p-3">
                    <form action="{{ route('faculty.chat.send') }}" method="POST">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $selectedUser->id }}">
                        <div class="input-group">
                            <input type="text" class="form-control py-2" name="message_text" placeholder="Type your message here..." required autofocus autocomplete="off">
                            <button class="btn btn-primary-grad px-4" type="submit">
                                <i class="fas fa-paper-plane me-1"></i> Send
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-5">
                    <div class="p-4 bg-light rounded-circle text-primary mb-3">
                        <i class="fas fa-comments fa-4x"></i>
                    </div>
                    <h4 class="fw-bold">Your Messages</h4>
                    <p class="text-muted" style="max-width: 400px;">Select a student from the directory list on the left to start a direct private conversation.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var container = document.getElementById('chat-messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>
@endsection
