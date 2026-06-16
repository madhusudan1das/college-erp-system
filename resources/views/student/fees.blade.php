@extends('layouts.app')

@section('title', 'Fees Portal')

@section('content')
<div class="row">
    <!-- Fee Dues & Details -->
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary-grad text-white">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-money-bill-wave me-2"></i>Fee Statements & Dues</h5>
            </div>
            <div class="card-body">
                @if($fees->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-file-invoice-dollar fa-3x mb-3 text-secondary"></i>
                        <p class="lead">No fee ledger details found. You have no pending dues!</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Description / Title</th>
                                    <th>Amount (INR)</th>
                                    <th>Status</th>
                                    <th>Transaction Ref</th>
                                    <th>Date Paid</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fees as $fee)
                                    <tr>
                                        <td class="fw-bold">{{ $fee->title }}</td>
                                        <td>₹{{ number_format($fee->amount, 2) }}</td>
                                        <td>
                                            @if($fee->status === 'paid')
                                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Paid</span>
                                            @else
                                                <span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i> Unpaid</span>
                                            @endif
                                        </td>
                                        <td><code>{{ $fee->transaction_no ?? 'N/A' }}</code></td>
                                        <td>{{ $fee->paid_at ? \Carbon\Carbon::parse($fee->paid_at)->format('d M Y, h:i A') : 'N/A' }}</td>
                                        <td>
                                            @if($fee->status === 'unpaid')
                                                <form action="{{ route('student.fees.pay', $fee->id) }}" method="POST" onsubmit="return confirm('Do you want to proceed with this simulated fee payment?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success w-100">
                                                        <i class="fas fa-credit-card me-1"></i> Pay Now
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('student.fees.receipt', $fee->id) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                                    <i class="fas fa-print me-1"></i> Print Receipt
                                                </a>
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
