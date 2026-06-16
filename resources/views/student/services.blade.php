@extends('layouts.app')

@section('title', 'Campus Services')

@section('content')
<div class="row">
    <!-- Hostel details card -->
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-primary-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-hotel me-2"></i>Hostel Allotment Details</h5>
            </div>
            <div class="card-body">
                @if($hostelAllotment)
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-3 bg-light rounded-circle text-primary me-3">
                            <i class="fas fa-hotel fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $hostelAllotment->hostel->name }}</h5>
                            <span class="text-muted text-uppercase small">{{ $hostelAllotment->hostel->type }}s Hostel</span>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                            <span>Room Allocated</span>
                            <strong class="text-primary h5 mb-0">Room No. {{ $hostelAllotment->room_no }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                            <span>Hostel Address</span>
                            <span class="text-muted">{{ $hostelAllotment->hostel->address ?? 'Main Campus' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                            <span>Allotted At</span>
                            <span class="text-muted">{{ \Carbon\Carbon::parse($hostelAllotment->alloted_at)->format('d M Y') }}</span>
                        </li>
                    </ul>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-bed fa-3x mb-3 text-secondary"></i>
                        <p class="lead mb-0">No Hostel room allotted yet.</p>
                        <small>Please contact the hostel administrator to get a room allocation.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Transport details card -->
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-success-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-bus me-2"></i>Transport / Bus Route Details</h5>
            </div>
            <div class="card-body">
                @if($transportAllotment)
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-3 bg-light rounded-circle text-success me-3">
                            <i class="fas fa-bus-alt fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $transportAllotment->transport->route_name }}</h5>
                            <span class="text-muted small">Vehicle: {{ $transportAllotment->transport->vehicle_no }}</span>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                            <span>Driver Name</span>
                            <strong class="text-dark">{{ $transportAllotment->transport->driver_name }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                            <span>Driver Phone</span>
                            <span class="text-primary fw-semibold"><i class="fas fa-phone me-1"></i> {{ $transportAllotment->transport->phone ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                            <span>Allotted At</span>
                            <span class="text-muted">{{ \Carbon\Carbon::parse($transportAllotment->alloted_at)->format('d M Y') }}</span>
                        </li>
                    </ul>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-bus fa-3x mb-3 text-secondary"></i>
                        <p class="lead mb-0">No bus routes allotted yet.</p>
                        <small>Apply at the transportation desk for bus route allocations.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Library records -->
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header py-3 bg-warning-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-book-reader me-2"></i>My Issued Books (Library)</h5>
            </div>
            <div class="card-body">
                @if($booksIssued->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-book-open fa-3x mb-3 text-secondary"></i>
                        <p class="lead mb-0">No library books issued to you currently.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Book Title</th>
                                    <th>Author</th>
                                    <th>ISBN</th>
                                    <th>Issued At</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Fine Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booksIssued as $issue)
                                    <tr>
                                        <td class="fw-bold">{{ $issue->book->title }}</td>
                                        <td>{{ $issue->book->author }}</td>
                                        <td><code>{{ $issue->book->isbn }}</code></td>
                                        <td>{{ \Carbon\Carbon::parse($issue->issued_at)->format('d M Y') }}</td>
                                        <td class="text-danger fw-semibold">{{ \Carbon\Carbon::parse($issue->return_due_date)->format('d M Y') }}</td>
                                        <td>
                                            @if($issue->returned_at)
                                                <span class="badge bg-success"><i class="fas fa-check-circle text-white"></i> Returned</span>
                                            @else
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Issued / Active</span>
                                            @endif
                                        </td>
                                        <td>₹{{ number_format($issue->fine_amount, 2) }}</td>
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
