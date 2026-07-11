@extends('layouts.app')

@section('title', 'Fees Portal - Razorpay')

@section('styles')
<style>
    /* Fee Summary Cards */
    .fee-summary-card {
        border-radius: 20px;
        padding: 28px;
        position: relative;
        overflow: hidden;
        color: #fff;
        min-height: 140px;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .fee-summary-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    .fee-summary-card .icon-bg {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 5rem;
        opacity: 0.12;
    }
    .fee-summary-card h2 {
        font-size: 2.2rem;
        font-weight: 800;
        margin: 0;
        letter-spacing: -1px;
    }
    .fee-summary-card .label {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        opacity: 0.85;
        margin-bottom: 8px;
    }
    .bg-total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .bg-paid { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .bg-pending { background: linear-gradient(135deg, #f12711 0%, #f5af19 100%); }

    /* Razorpay Badge */
    .razorpay-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        background: linear-gradient(135deg, #1a1f3c, #2b3170);
        color: #fff;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .razorpay-badge img {
        height: 16px;
        filter: brightness(10);
    }

    /* Fee Table */
    .fees-table {
        border-radius: 16px;
        overflow: hidden;
    }
    .fees-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
    }
    .fees-table thead th {
        border: none;
        padding: 16px 18px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .fees-table tbody td {
        padding: 16px 18px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f2f5;
    }
    .fees-table tbody tr {
        transition: all 0.2s;
    }
    .fees-table tbody tr:hover {
        background: #f8faff;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .status-paid {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }
    .status-unpaid {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
    }

    /* Pay Button */
    .btn-razorpay {
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 10px 22px;
        font-weight: 700;
        font-size: 0.85rem;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }
    .btn-razorpay:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        color: #fff;
    }
    .btn-razorpay:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    .btn-razorpay .spinner-border {
        width: 16px;
        height: 16px;
        border-width: 2px;
    }

    /* Receipt Button */
    .btn-receipt {
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
        color: #0369a1;
        border: 1px solid #bae6fd;
        border-radius: 12px;
        padding: 10px 22px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    .btn-receipt:hover {
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        color: #0c4a6e;
        transform: translateY(-1px);
    }

    /* Success Overlay */
    .payment-success-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(8px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .payment-success-overlay.active {
        display: flex;
    }
    .success-card {
        background: #fff;
        border-radius: 24px;
        padding: 50px;
        text-align: center;
        max-width: 460px;
        width: 90%;
        animation: successPop 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 25px 60px rgba(0,0,0,0.2);
    }
    .success-checkmark {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #10b981, #34d399);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: checkBounce 0.6s ease 0.3s both;
    }
    .success-checkmark i {
        font-size: 2.5rem;
        color: #fff;
    }
    @keyframes successPop {
        from { opacity: 0; transform: scale(0.8) translateY(30px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes checkBounce {
        0% { transform: scale(0); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    /* Amount Display */
    .amount-display {
        font-family: 'Inter', monospace;
        font-weight: 800;
        font-size: 1.1rem;
        color: #1e293b;
    }

    /* Razorpay ID display */
    .razorpay-id {
        font-family: monospace;
        font-size: 0.78rem;
        color: #6366f1;
        background: #eef2ff;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-block;
    }

    /* Card header */
    .fees-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border-radius: 16px 16px 0 0 !important;
        padding: 20px 24px;
    }

    /* Test mode banner */
    .test-mode-banner {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        border-radius: 12px;
        padding: 12px 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 24px;
        border: 1px solid #f59e0b;
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- Test Mode Banner -->
    <div class="col-12">
        <div class="test-mode-banner">
            <i class="fas fa-flask"></i>
            <span>Razorpay Test Mode — No real money will be charged. Use test card <code style="background: #fff; padding: 2px 8px; border-radius: 4px; color: #92400e;">4111 1111 1111 1111</code> for testing.</span>
        </div>
    </div>

    <!-- Fee Summary Cards -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="fee-summary-card bg-total">
            <div class="label"><i class="fas fa-file-invoice-dollar me-1"></i> Total Fees</div>
            <h2>₹{{ number_format($fees->sum('amount'), 2) }}</h2>
            <p class="mb-0 mt-2 small opacity-75">{{ $fees->count() }} fee entries</p>
            <div class="icon-bg"><i class="fas fa-file-invoice-dollar"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="fee-summary-card bg-paid">
            <div class="label"><i class="fas fa-check-circle me-1"></i> Paid Amount</div>
            <h2>₹{{ number_format($fees->where('status', 'paid')->sum('amount'), 2) }}</h2>
            <p class="mb-0 mt-2 small opacity-75">{{ $fees->where('status', 'paid')->count() }} payments done</p>
            <div class="icon-bg"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-md-12 mb-4">
        <div class="fee-summary-card bg-pending">
            <div class="label"><i class="fas fa-clock me-1"></i> Pending Dues</div>
            <h2>₹{{ number_format($fees->where('status', 'unpaid')->sum('amount'), 2) }}</h2>
            <p class="mb-0 mt-2 small opacity-75">{{ $fees->where('status', 'unpaid')->count() }} pending</p>
            <div class="icon-bg"><i class="fas fa-hourglass-half"></i></div>
        </div>
    </div>

    <!-- Fees Table -->
    <div class="col-12">
        <div class="card shadow-sm" style="border-radius: 16px; border: none;">
            <div class="fees-card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold"><i class="fas fa-money-bill-wave me-2"></i>Fee Statements & Dues</h5>
                <div class="razorpay-badge">
                    <i class="fas fa-shield-alt"></i>
                    Secured by Razorpay
                </div>
            </div>
            <div class="card-body p-0">
                @if($fees->isEmpty())
                    <div class="text-center py-5 px-4">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #e0e7ff, #c7d2fe); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <i class="fas fa-file-invoice-dollar fa-2x" style="color: #6366f1;"></i>
                        </div>
                        <h5 class="fw-bold text-dark">No Fee Records</h5>
                        <p class="text-muted mb-0">You have no pending dues or fee entries at this time.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table fees-table mb-0">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Transaction Ref</th>
                                    <th>Payment ID</th>
                                    <th>Date Paid</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fees as $fee)
                                    <tr id="fee-row-{{ $fee->id }}">
                                        <td>
                                            <div class="fw-bold text-dark">{{ $fee->title }}</div>
                                        </td>
                                        <td>
                                            <span class="amount-display">₹{{ number_format($fee->amount, 2) }}</span>
                                        </td>
                                        <td>
                                            @if($fee->status === 'paid')
                                                <span class="status-badge status-paid">
                                                    <i class="fas fa-check-circle"></i> Paid
                                                </span>
                                            @else
                                                <span class="status-badge status-unpaid" id="status-{{ $fee->id }}">
                                                    <i class="fas fa-exclamation-triangle"></i> Unpaid
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <code style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem;">{{ $fee->transaction_no ?? 'N/A' }}</code>
                                        </td>
                                        <td>
                                            @if($fee->razorpay_payment_id)
                                                <span class="razorpay-id">{{ $fee->razorpay_payment_id }}</span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($fee->paid_at)
                                                <span class="small fw-semibold">{{ \Carbon\Carbon::parse($fee->paid_at)->format('d M Y') }}</span>
                                                <br>
                                                <span class="text-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($fee->paid_at)->format('h:i A') }}</span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($fee->status === 'unpaid')
                                                <button type="button"
                                                        class="btn-razorpay pay-btn"
                                                        data-fee-id="{{ $fee->id }}"
                                                        data-fee-amount="{{ $fee->amount }}"
                                                        data-fee-title="{{ $fee->title }}"
                                                        id="pay-btn-{{ $fee->id }}">
                                                    <i class="fas fa-bolt"></i>
                                                    <span class="btn-text">Pay with Razorpay</span>
                                                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                                </button>
                                            @else
                                                <a href="{{ route('student.fees.receipt', $fee->id) }}" target="_blank" class="btn-receipt">
                                                    <i class="fas fa-print"></i> Receipt
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

<!-- Payment Success Overlay -->
<div class="payment-success-overlay" id="paymentSuccessOverlay">
    <div class="success-card">
        <div class="success-checkmark">
            <i class="fas fa-check"></i>
        </div>
        <h4 class="fw-bold text-dark mb-2">Payment Successful!</h4>
        <p class="text-muted mb-3">Your fee has been paid successfully via Razorpay.</p>
        <div class="mb-3 p-3" style="background: #f0fdf4; border-radius: 12px;">
            <div class="small text-muted mb-1">Transaction ID</div>
            <div class="fw-bold text-dark" id="successTxnId">—</div>
        </div>
        <div class="mb-4 p-3" style="background: #eef2ff; border-radius: 12px;">
            <div class="small text-muted mb-1">Razorpay Payment ID</div>
            <div class="fw-bold" style="color: #6366f1;" id="successRzpId">—</div>
        </div>
        <div class="d-flex gap-2 justify-content-center">
            <a href="#" id="successReceiptLink" target="_blank" class="btn-receipt">
                <i class="fas fa-print"></i> View Receipt
            </a>
            <button type="button" onclick="closeSuccessOverlay()" class="btn btn-outline-secondary" style="border-radius: 12px; padding: 10px 22px; font-weight: 600;">
                <i class="fas fa-times me-1"></i> Close
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    const RAZORPAY_KEY = "{{ $razorpayKey }}";
    const CSRF_TOKEN = "{{ csrf_token() }}";

    // Handle Pay button click
    document.querySelectorAll('.pay-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const feeId = this.dataset.feeId;
            const feeAmount = this.dataset.feeAmount;
            const feeTitle = this.dataset.feeTitle;
            const button = this;

            // Show loading state
            button.disabled = true;
            button.querySelector('.btn-text').textContent = 'Processing...';
            button.querySelector('.spinner-border').classList.remove('d-none');

            // Step 1: Create Razorpay Order
            fetch("{{ url('student/fees') }}/" + feeId + "/initiate", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    resetButton(button);
                    return;
                }

                // Step 2: Open Razorpay Checkout
                const options = {
                    key: RAZORPAY_KEY,
                    amount: data.amount,
                    currency: data.currency,
                    name: "College ERP",
                    description: feeTitle,
                    order_id: data.order_id,
                    prefill: {
                        name: data.student_name,
                        email: data.student_email,
                        contact: data.student_phone,
                    },
                    theme: {
                        color: "#6366f1",
                        backdrop_color: "rgba(0, 0, 0, 0.6)"
                    },
                    modal: {
                        ondismiss: function() {
                            resetButton(button);
                        }
                    },
                    handler: function(response) {
                        // Step 3: Verify Payment
                        verifyPayment(response, feeId, button);
                    }
                };

                const rzp = new Razorpay(options);
                rzp.on('payment.failed', function(response) {
                    alert('Payment failed: ' + response.error.description);
                    resetButton(button);
                });
                rzp.open();

                // Reset button text (Razorpay popup is now open)
                button.querySelector('.btn-text').textContent = 'Complete Payment';
                button.querySelector('.spinner-border').classList.add('d-none');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to initiate payment. Please try again.');
                resetButton(button);
            });
        });
    });

    function verifyPayment(razorpayResponse, feeId, button) {
        button.disabled = true;
        button.querySelector('.btn-text').textContent = 'Verifying...';
        button.querySelector('.spinner-border').classList.remove('d-none');

        fetch("{{ route('student.fees.verify') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                razorpay_payment_id: razorpayResponse.razorpay_payment_id,
                razorpay_order_id: razorpayResponse.razorpay_order_id,
                razorpay_signature: razorpayResponse.razorpay_signature,
                fee_id: feeId,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success overlay
                showSuccessOverlay(data.transaction_no, data.razorpay_payment_id, data.receipt_url);

                // Update the table row
                const row = document.getElementById('fee-row-' + feeId);
                if (row) {
                    // Update status
                    const statusCell = row.cells[2];
                    statusCell.innerHTML = '<span class="status-badge status-paid"><i class="fas fa-check-circle"></i> Paid</span>';

                    // Update transaction ref
                    const txnCell = row.cells[3];
                    txnCell.innerHTML = '<code style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem;">' + data.transaction_no + '</code>';

                    // Update Razorpay ID
                    const rzpCell = row.cells[4];
                    rzpCell.innerHTML = '<span class="razorpay-id">' + data.razorpay_payment_id + '</span>';

                    // Update date
                    const dateCell = row.cells[5];
                    const now = new Date();
                    dateCell.innerHTML = '<span class="small fw-semibold">' + now.toLocaleDateString('en-IN', {day: '2-digit', month: 'short', year: 'numeric'}) + '</span><br><span class="text-muted" style="font-size: 0.75rem;">' + now.toLocaleTimeString('en-IN', {hour: '2-digit', minute: '2-digit'}) + '</span>';

                    // Update action
                    const actionCell = row.cells[6];
                    actionCell.innerHTML = '<a href="' + data.receipt_url + '" target="_blank" class="btn-receipt"><i class="fas fa-print"></i> Receipt</a>';
                }

                // Update summary cards
                setTimeout(function() { location.reload(); }, 3000);
            } else {
                alert(data.error || 'Payment verification failed.');
                resetButton(button);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Payment verification failed. Please contact administration.');
            resetButton(button);
        });
    }

    function resetButton(button) {
        button.disabled = false;
        button.querySelector('.btn-text').textContent = 'Pay with Razorpay';
        button.querySelector('.spinner-border').classList.add('d-none');
    }

    function showSuccessOverlay(txnId, rzpId, receiptUrl) {
        document.getElementById('successTxnId').textContent = txnId;
        document.getElementById('successRzpId').textContent = rzpId;
        document.getElementById('successReceiptLink').href = receiptUrl;
        document.getElementById('paymentSuccessOverlay').classList.add('active');
    }

    function closeSuccessOverlay() {
        document.getElementById('paymentSuccessOverlay').classList.remove('active');
    }
</script>
@endsection
