@extends('layouts.app')

@section('title', 'Notices')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Notice Board</h2>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-light">
        <h6 class="m-0 font-weight-bold text-primary">Notice Announcements</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Announcement</th>
                        <th>Attachment</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($notices as $notice)
                    <tr>
                        <td data-sort="{{ strtotime($notice->created_at) }}">
                            {{ $notice->created_at->format('d M Y h:i A') }}
                        </td>
                        <td><strong>{{ $notice->title }}</strong></td>
                        <td>{{ $notice->content }}</td>
                        <td>
                            @if ($notice->attachment)
                                <a href="{{ asset($notice->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-paperclip"></i> View</a>
                            @else
                                -
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
