@extends('layouts.app')

@section('title', 'Hostel Management')

@section('content')
<div class="row">
    <!-- Left column: forms -->
    <div class="col-md-4">
        <!-- Create Hostel Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-plus-circle me-2"></i>Create Hostel Building</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.hostels.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Hostel Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Newton Boys Hostel..." required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="boys">Boys Hostel</option>
                            <option value="girls">Girls Hostel</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacity (Total Rooms/Beds)</label>
                        <input type="number" class="form-control" id="capacity" name="capacity" min="1" placeholder="e.g. 150" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Location Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="e.g. Block C, East Campus..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary-grad w-100"><i class="fas fa-save me-1"></i> Add Building</button>
                </form>
            </div>
        </div>

        <!-- Allot Room Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-bed me-2"></i>Allot Room to Student</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.hostels.allot') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="hostel_id" class="form-label">Select Hostel</label>
                        <select class="form-select" id="hostel_id" name="hostel_id" required>
                            <option value="">-- Select Hostel --</option>
                            @foreach($hostels as $h)
                                <option value="{{ $h->id }}">{{ $h->name }} ({{ ucfirst($h->type) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="room_no" class="form-label">Room Number</label>
                        <input type="text" class="form-control" id="room_no" name="room_no" placeholder="e.g. 102-A" required>
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
                    <button type="submit" class="btn btn-success-grad w-100"><i class="fas fa-check-circle me-1"></i> Allot Room</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right column: tables -->
    <div class="col-md-8">
        <!-- Hostels List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-hotel me-2"></i>Hostel Buildings</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Capacity</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hostels as $h)
                                <tr>
                                    <td class="fw-bold">{{ $h->name }}</td>
                                    <td class="text-uppercase small">{{ $h->type }}</td>
                                    <td>{{ $h->capacity }} beds</td>
                                    <td>{{ $h->address ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Room Allotments List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h5 class="m-0 font-weight-bold text-success"><i class="fas fa-users me-2"></i>Current Room Allotments</h5>
            </div>
            <div class="card-body">
                @if($allotments->isEmpty())
                    <div class="text-center py-4 text-muted">
                        No active hostel room allotments.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Hostel</th>
                                    <th>Room No</th>
                                    <th>Student Name</th>
                                    <th>Enrollment No</th>
                                    <th>Allotted At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allotments as $allot)
                                    <tr>
                                        <td>{{ $allot->hostel->name }}</td>
                                        <td class="fw-bold text-primary">{{ $allot->room_no }}</td>
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
