<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Payment Receipt - {{ $fee->transaction_no }}</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .receipt-card {
            background-color: #fff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            max-width: 800px;
            margin: 40px auto;
        }
        .receipt-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 30px;
            border-radius: 15px 15px 0 0;
        }
        .receipt-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 8rem;
            font-weight: 800;
            color: rgba(40, 167, 69, 0.08);
            z-index: 0;
            pointer-events: none;
            user-select: none;
            letter-spacing: 5px;
        }
        @media print {
            body {
                background-color: #fff;
            }
            .receipt-card {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="py-4">

<div class="container position-relative">
    <div class="d-flex justify-content-end no-print mb-3 max-width-800 mx-auto" style="max-width: 800px;">
        <button onclick="window.print();" class="btn btn-primary shadow-sm"><i class="fas fa-print me-2"></i>Print Receipt</button>
        <button onclick="window.close();" class="btn btn-secondary shadow-sm ms-2"><i class="fas fa-times me-2"></i>Close Window</button>
    </div>

    <div class="card receipt-card position-relative overflow-hidden">
        <div class="receipt-watermark">PAID</div>
        
        <!-- Header -->
        <div class="receipt-header d-flex justify-content-between align-items-center position-relative" style="z-index: 1;">
            <div>
                <h3 class="mb-1 fw-bold">COLLEGE ACADEMIC ERP</h3>
                <p class="mb-0 opacity-75 small"><i class="fas fa-map-marker-alt me-1"></i> Campus Main Road, Education District</p>
            </div>
            <div class="text-end">
                <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm fs-6"><i class="fas fa-check-circle me-1"></i> PAID</span>
            </div>
        </div>

        <div class="card-body p-5 position-relative" style="z-index: 1;">
            <div class="row mb-4">
                <div class="col-6">
                    <label class="text-uppercase small fw-bold text-muted d-block">Transaction Ref</label>
                    <span class="font-monospace fw-bold text-primary">{{ $fee->transaction_no }}</span>
                </div>
                <div class="col-6 text-end">
                    <label class="text-uppercase small fw-bold text-muted d-block">Payment Date</label>
                    <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($fee->paid_at)->format('d M Y, h:i A') }}</span>
                </div>
            </div>

            <hr class="my-4 opacity-25">

            <!-- Student Info -->
            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-user-graduate me-2 text-muted"></i>Student Information</h5>
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <label class="text-uppercase small fw-bold text-muted d-block">Student Name</label>
                    <span class="text-dark fw-semibold">{{ $student->first_name }} {{ $student->last_name }}</span>
                </div>
                <div class="col-sm-6">
                    <label class="text-uppercase small fw-bold text-muted d-block">Enrollment Number</label>
                    <span class="text-dark fw-semibold font-monospace">{{ $student->enrollment_no }}</span>
                </div>
                <div class="col-sm-6">
                    <label class="text-uppercase small fw-bold text-muted d-block">Course & Branch</label>
                    <span class="text-dark fw-semibold">{{ $student->course->name }}</span>
                </div>
                <div class="col-sm-6">
                    <label class="text-uppercase small fw-bold text-muted d-block">Current Semester</label>
                    <span class="text-dark fw-semibold">Semester {{ $student->current_semester }}</span>
                </div>
            </div>

            <hr class="my-4 opacity-25">

            <!-- Payment Details Table -->
            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-receipt me-2 text-muted"></i>Fee Description</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th width="30%" class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold">{{ $fee->title }}</td>
                            <td class="text-end fw-bold">₹{{ number_format($fee->amount, 2) }}</td>
                        </tr>
                        <tr class="table-light">
                            <td class="text-end fw-bold">Total Paid</td>
                            <td class="text-end fw-bold text-success" style="font-size: 1.1rem;">₹{{ number_format($fee->amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer Details -->
            <div class="row mt-5 pt-4 border-top">
                <div class="col-8">
                    <p class="text-muted small mb-0">This is a computer-generated transaction receipt for fees paid online through the College Student Portal. No physical signature is required.</p>
                </div>
                <div class="col-4 text-end d-flex flex-column align-items-end justify-content-end">
                    <div class="border-top pt-2 w-75 text-center small fw-bold text-muted">
                        Accounts Officer
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
