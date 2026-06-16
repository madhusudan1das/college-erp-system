@extends('layouts.app')

@section('title', 'Fee Dues Manager')

@section('content')
<div class="row">
    <!-- Generate Dues Form -->
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-plus-circle me-2"></i>Generate Student Fee Due</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.fees.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Filters (to restrict student list)</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <select id="filter_dept" class="form-select form-select-sm">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <select id="filter_sem" class="form-select form-select-sm">
                                    <option value="">All Semesters</option>
                                    @for($i = 1; $i <= 8; $i++)
                                        <option value="{{ $i }}">Sem {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">Select Students</label>
                            <span class="small text-muted" id="selected_count">0 selected</span>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="selectAllStudentsForFees" style="cursor: pointer;">
                            <label class="form-check-label small fw-bold" for="selectAllStudentsForFees" style="cursor: pointer;">
                                Select All Visible
                            </label>
                        </div>
                        
                        <div id="studentChecklistContainer" class="border rounded p-2" style="max-height: 220px; overflow-y: auto; background-color: #fafafa;">
                            @foreach($students as $stud)
                                <div class="form-check student-fee-row mb-2" 
                                     data-dept-id="{{ $stud->course->department_id ?? '' }}" 
                                     data-semester="{{ $stud->current_semester }}">
                                    <input class="form-check-input student-fee-checkbox" type="checkbox" name="student_ids[]" value="{{ $stud->id }}" id="stud_fee_{{ $stud->id }}" style="cursor: pointer;">
                                    <label class="form-check-label small" for="stud_fee_{{ $stud->id }}" style="cursor: pointer; line-height: 1.4;">
                                        <strong>{{ $stud->first_name }} {{ $stud->last_name }}</strong><br>
                                        <span class="text-muted font-monospace" style="font-size: 0.75rem;">{{ $stud->enrollment_no }} | Sem {{ $stud->current_semester }} | {{ $stud->course->name }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Fee Statement Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="e.g. Semester 3 Tuition Fee..." required>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Fee Amount (INR)</label>
                        <input type="number" class="form-control" id="amount" name="amount" min="0" step="0.01" placeholder="e.g. 45000" required>
                    </div>
                    <button type="submit" class="btn btn-primary-grad w-100"><i class="fas fa-check-circle me-1"></i> Generate Due</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Fees Statement ledger -->
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-alt me-2"></i>Fee Accounts Ledger</h5>
            </div>
            <div class="card-body">
                @if($fees->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-file-invoice-dollar fa-3x mb-3 text-secondary"></i>
                        <p class="lead">No student fee statements generated yet.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Description</th>
                                    <th>Amount (INR)</th>
                                    <th>Status</th>
                                    <th>Payment Ref</th>
                                    <th>Paid At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fees as $fee)
                                    <tr>
                                        <td class="fw-bold">{{ $fee->student->first_name }} {{ $fee->student->last_name }}<br><small class="text-muted">Enroll: {{ $fee->student->enrollment_no }}</small></td>
                                        <td>{{ $fee->student->course->name }}</td>
                                        <td>{{ $fee->title }}</td>
                                        <td>₹{{ number_format($fee->amount, 2) }}</td>
                                        <td>
                                            @if($fee->status === 'paid')
                                                <span class="badge bg-success"><i class="fas fa-check-circle text-white"></i> Paid</span>
                                            @else
                                                <span class="badge bg-danger"><i class="fas fa-exclamation-triangle text-white"></i> Unpaid</span>
                                            @endif
                                        </td>
                                        <td><code>{{ $fee->transaction_no ?? 'N/A' }}</code></td>
                                        <td>{{ $fee->paid_at ? \Carbon\Carbon::parse($fee->paid_at)->format('d M Y, h:i A') : 'N/A' }}</td>
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

@section('scripts')
<script>
$(document).ready(function() {
    function filterStudents() {
        const deptId = $('#filter_dept').val();
        const sem = $('#filter_sem').val();
        
        $('.student-fee-row').each(function() {
            const row = $(this);
            const rowDept = row.data('dept-id');
            const rowSem = row.data('semester');
            
            const matchDept = !deptId || (rowDept == deptId);
            const matchSem = !sem || (rowSem == sem);
            
            if (matchDept && matchSem) {
                row.show();
            } else {
                row.hide();
                // Uncheck if hidden
                row.find('.student-fee-checkbox').prop('checked', false);
            }
        });
        
        updateSelectedCount();
    }
    
    function updateSelectedCount() {
        const checkedVisible = $('.student-fee-checkbox:visible:checked').length;
        const totalVisible = $('.student-fee-checkbox:visible').length;
        
        $('#selected_count').text(checkedVisible + ' selected');
        $('#selectAllStudentsForFees').prop('checked', totalVisible > 0 && totalVisible === checkedVisible);
    }
    
    // Trigger filter on dropdown change
    $('#filter_dept, #filter_sem').on('change', function() {
        filterStudents();
    });
    
    // Select All visible checkboxes
    $('#selectAllStudentsForFees').on('change', function() {
        const checked = $(this).is(':checked');
        $('.student-fee-checkbox:visible').prop('checked', checked);
        updateSelectedCount();
    });
    
    // Individual checkbox change
    $(document).on('change', '.student-fee-checkbox', function() {
        updateSelectedCount();
    });
    
    // Initialize
    filterStudents();
});
</script>
@endsection
