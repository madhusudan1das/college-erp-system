@extends('layouts.app')

@section('title', 'Transport Management')

@section('content')
<div class="row">
    <!-- Left column: forms -->
    <div class="col-md-4">
        <!-- Create Transport Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-plus-circle me-2"></i>Create Bus Route</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.transports.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="route_name" class="form-label">Route Name</label>
                        <input type="text" class="form-control" id="route_name" name="route_name" placeholder="e.g. Route 1 - City Center to Campus..." required>
                    </div>
                    <div class="mb-3">
                        <label for="vehicle_no" class="form-label">Vehicle Registration Number</label>
                        <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" placeholder="e.g. DL-1CA-1234" required>
                    </div>
                    <div class="mb-3">
                        <label for="driver_name" class="form-label">Driver Name</label>
                        <input type="text" class="form-control" id="driver_name" name="driver_name" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Driver Contact Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="e.g. 9876543210">
                    </div>
                    <button type="submit" class="btn btn-primary-grad w-100"><i class="fas fa-save me-1"></i> Add Bus Route</button>
                </form>
            </div>
        </div>

        <!-- Allot Transport Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-bus me-2"></i>Allot Route to Student</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.transports.allot') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="transport_id" class="form-label">Select Bus Route</label>
                        <select class="form-select" id="transport_id" name="transport_id" required>
                            <option value="">-- Select Route --</option>
                            @foreach($transports as $t)
                                <option value="{{ $t->id }}">{{ $t->route_name }} ({{ $t->vehicle_no }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Select Student</label>
                        <select class="form-select" id="student_id" name="student_id" required>
                            <option value="">-- Select Student --</option>
                            @foreach($students as $stud)
                                <option value="{{ $stud->id }}">{{ $stud->first_name }} {{ $stud->last_name }} ({{ $stud->enrollment_no }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success-grad w-100"><i class="fas fa-check-circle me-1"></i> Allot Route</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right column: tables -->
    <div class="col-md-8">
        <!-- Transports List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-route me-2"></i>Available Bus Routes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Route Name</th>
                                <th>Vehicle No</th>
                                <th>Driver Name</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transports as $t)
                                <tr>
                                    <td class="fw-bold">{{ $t->route_name }}</td>
                                    <td><code>{{ $t->vehicle_no }}</code></td>
                                    <td>{{ $t->driver_name }}</td>
                                    <td>{{ $t->phone ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Transport Allotments List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h5 class="m-0 font-weight-bold text-success"><i class="fas fa-users me-2"></i>Route Passengers Allotments</h5>
            </div>
            <div class="card-body">
                @if($allotments->isEmpty())
                    <div class="text-center py-4 text-muted">
                        No active bus route allotments.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Route</th>
                                    <th>Vehicle No</th>
                                    <th>Student Name</th>
                                    <th>Enrollment No</th>
                                    <th>Allotted At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allotments as $allot)
                                    <tr>
                                        <td>{{ $allot->transport->route_name }}</td>
                                        <td><code>{{ $allot->transport->vehicle_no }}</code></td>
                                        <td>{{ $allot->student->first_name }} {{ $allot->student->last_name }}</td>
                                        <td><code>{{ $allot->student->enrollment_no }}</code></td>
                                        <td>{{ \Carbon\Carbon::parse($allot->alloted_at)->format('d M Y') }}</td>
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
