@extends('layouts.app')

@section('title', 'Library Management')

@section('content')
<div class="row">
    <!-- Left column: forms -->
    <div class="col-md-4">
        <!-- Add Book Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-plus-circle me-2"></i>Add Book to Catalog</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.library.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Book Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="e.g. Introduction to Algorithms..." required>
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label">Author Name</label>
                        <input type="text" class="form-control" id="author" name="author" placeholder="e.g. Thomas H. Cormen" required>
                    </div>
                    <div class="mb-3">
                        <label for="isbn" class="form-label">ISBN Number</label>
                        <input type="text" class="form-control" id="isbn" name="isbn" placeholder="e.g. 978-0262033848" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="category" name="category" placeholder="e.g. Computer Science..." required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity (Total Copies)</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" placeholder="e.g. 10" required>
                    </div>
                    <button type="submit" class="btn btn-primary-grad w-100"><i class="fas fa-save me-1"></i> Add Book</button>
                </form>
            </div>
        </div>

        <!-- Issue Book Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-book me-2"></i>Issue Book to User</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.library.issue') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="book_id" class="form-label">Select Book</label>
                        <select class="form-select" id="book_id" name="book_id" required>
                            <option value="">-- Select Book --</option>
                            @foreach($books as $b)
                                @if($b->available_quantity > 0)
                                    <option value="{{ $b->id }}">{{ $b->title }} (Available: {{ $b->available_quantity }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Select User (Student/Faculty)</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">-- Select User --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">
                                    {{ $u->username }} 
                                    @if($u->student)
                                        (Student: {{ $u->student->first_name }} {{ $u->student->last_name }})
                                    @elseif($u->faculty)
                                        (Faculty: Prof. {{ $u->faculty->first_name }} {{ $u->faculty->last_name }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="return_due_date" class="form-label">Return Due Date</label>
                        <input type="date" class="form-control" id="return_due_date" name="return_due_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    </div>
                    <button type="submit" class="btn btn-success-grad w-100"><i class="fas fa-check-circle me-1"></i> Issue Book</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right column: tables -->
    <div class="col-md-8">
        <!-- Catalog List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-list me-2"></i>Library Catalog (Books)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped datatable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>ISBN</th>
                                <th>Category</th>
                                <th>Qty</th>
                                <th>Available</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($books as $b)
                                <tr>
                                    <td class="fw-bold">{{ $b->title }}</td>
                                    <td>{{ $b->author }}</td>
                                    <td><code>{{ $b->isbn }}</code></td>
                                    <td>{{ $b->category }}</td>
                                    <td>{{ $b->quantity }}</td>
                                    <td>
                                        <span class="badge bg-{{ $b->available_quantity > 0 ? 'success' : 'danger' }}">
                                            {{ $b->available_quantity }} left
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Book Issues List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h5 class="m-0 font-weight-bold text-success"><i class="fas fa-history me-2"></i>Book Borrowing Logs</h5>
            </div>
            <div class="card-body">
                @if($issues->isEmpty())
                    <div class="text-center py-4 text-muted">
                        No book borrow records registered.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Book</th>
                                    <th>User</th>
                                    <th>Issued At</th>
                                    <th>Due Date</th>
                                    <th>Returned At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($issues as $issue)
                                    <tr>
                                        <td class="fw-bold">{{ $issue->book->title }}</td>
                                        <td>
                                            {{ $issue->user->username }}
                                            @if($issue->user->student)
                                                <small class="text-muted"><br>Student: {{ $issue->user->student->first_name }}</small>
                                            @elseif($issue->user->faculty)
                                                <small class="text-muted"><br>Faculty: Prof. {{ $issue->user->faculty->first_name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($issue->issued_at)->format('d M Y') }}</td>
                                        <td class="text-danger fw-semibold">{{ \Carbon\Carbon::parse($issue->return_due_date)->format('d M Y') }}</td>
                                        <td>
                                            @if($issue->returned_at)
                                                <span class="badge bg-success"><i class="fas fa-check-circle text-white"></i> {{ \Carbon\Carbon::parse($issue->returned_at)->format('d M Y') }}</span>
                                            @else
                                                <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half text-dark"></i> Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$issue->returned_at)
                                                <form action="{{ route('admin.library.return', $issue->id) }}" method="POST" onsubmit="return confirm('Process return for this book?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-undo"></i> Return
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small">Completed</span>
                                            @endif
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
