@extends('layouts.app')

@section('title', 'Faculty Payroll Management')

@section('content')
<div class="row">
    <!-- Log Salary Payment Form -->
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-plus-circle me-2"></i>Process Salary Payment</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payroll.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="faculty_id" class="form-label">Select Faculty Member</label>
                        <select class="form-select" id="faculty_id" name="faculty_id" required>
                            <option value="">-- Select Faculty --</option>
                            @foreach($faculty as $fac)
                                <option value="{{ $fac->id }}">Prof. {{ $fac->first_name }} {{ $fac->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="base_salary" class="form-label">Base Salary (INR)</label>
                        <input type="number" class="form-control" id="base_salary" name="base_salary" min="0" step="0.01" placeholder="e.g. 75000" required>
                    </div>
                    <div class="mb-3">
                        <label for="bonuses" class="form-label">Bonuses / Incentives (INR)</label>
                        <input type="number" class="form-control" id="bonuses" name="bonuses" min="0" step="0.01" value="0.00">
                    </div>
                    <div class="mb-3">
                        <label for="deductions" class="form-label">Deductions (INR)</label>
                        <input type="number" class="form-control" id="deductions" name="deductions" min="0" step="0.01" value="0.00">
                    </div>
                    <div class="mb-3">
                        <label for="pay_date" class="form-label">Pay Date</label>
                        <input type="date" class="form-control" id="pay_date" name="pay_date" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary-grad w-100"><i class="fas fa-wallet me-1"></i> Disburse Salary</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Salary Records Ledger -->
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-receipt me-2"></i>Payroll Payments Ledger</h5>
            </div>
            <div class="card-body">
                @if($salaries->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-wallet fa-3x mb-3 text-secondary"></i>
                        <p class="lead">No salary payments processed yet.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Faculty</th>
                                    <th>Base Salary</th>
                                    <th>Bonuses</th>
                                    <th>Deductions</th>
                                    <th>Total Disbursed</th>
                                    <th>Pay Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salaries as $sal)
                                    <tr>
                                        <td class="fw-bold">Prof. {{ $sal->faculty->first_name }} {{ $sal->faculty->last_name }}</td>
                                        <td>₹{{ number_format($sal->base_salary, 2) }}</td>
                                        <td class="text-success">+ ₹{{ number_format($sal->bonuses, 2) }}</td>
                                        <td class="text-danger">- ₹{{ number_format($sal->deductions, 2) }}</td>
                                        <td class="fw-bold text-primary">₹{{ number_format($sal->base_salary + $sal->bonuses - $sal->deductions, 2) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($sal->pay_date)->format('d M Y') }}</td>
                                        <td>
                                            @if($sal->status === 'paid')
                                                <span class="badge bg-success"><i class="fas fa-check-circle text-white"></i> Paid</span>
                                            @else
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock text-dark"></i> Pending</span>
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
