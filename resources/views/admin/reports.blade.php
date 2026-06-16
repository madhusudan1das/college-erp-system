@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-bar me-2"></i>Reports & Institutional Analytics</h2>
</div>

<div class="row">
    <!-- Stat card 1: Collections -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card bg-primary-grad shadow h-100 py-2">
            <div>
                <div class="text-xs font-weight-bold text-uppercase mb-1">Total Fees Collected</div>
                <div class="h3 mb-0 font-weight-bold">₹{{ number_format($paidFees, 2) }}</div>
            </div>
            <div class="icon">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
        </div>
    </div>

    <!-- Stat card 2: Outstanding Dues -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card bg-danger-grad shadow h-100 py-2">
            <div>
                <div class="text-xs font-weight-bold text-uppercase mb-1">Pending Fees Dues</div>
                <div class="h3 mb-0 font-weight-bold">₹{{ number_format($unpaidFees, 2) }}</div>
            </div>
            <div class="icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
        </div>
    </div>

    <!-- Stat card 3: Library Issued Books -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card bg-success-grad shadow h-100 py-2">
            <div>
                <div class="text-xs font-weight-bold text-uppercase mb-1">Books Out / Borrowed</div>
                <div class="h3 mb-0 font-weight-bold">{{ $issuedBooks }} / {{ $totalBooks }}</div>
            </div>
            <div class="icon">
                <i class="fas fa-book-reader"></i>
            </div>
        </div>
    </div>

    <!-- Stat card 4: Pending Leaves -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card bg-warning-grad shadow h-100 py-2">
            <div>
                <div class="text-xs font-weight-bold text-uppercase mb-1">Pending Leaves</div>
                <div class="h3 mb-0 font-weight-bold">{{ $leaveStats['pending'] }}</div>
            </div>
            <div class="icon">
                <i class="fas fa-plane-departure"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Chart 1: Financial Dues vs Collected -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie me-2"></i>Fees Financial Health Overview</h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="max-height: 250px; width: 100%;">
                    <canvas id="feesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 2: Leaves Stats -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar me-2"></i>Leave Applications Status Breakdown</h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="max-height: 250px; width: 100%;">
                    <canvas id="leavesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <!-- Academic performance -->
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-poll-h me-2"></i>Academic Marks Average Performance</h6>
            </div>
            <div class="card-body">
                @if($marksAverage->isEmpty())
                    <div class="text-center py-4 text-muted">
                        No examination records or grades submitted yet to map academic analytics.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Exam Category / Assessment Type</th>
                                    <th>Average Percentage Score</th>
                                    <th>Progress Bar Indicator</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($marksAverage as $mAvg)
                                    <tr>
                                        <td class="fw-bold">{{ ucfirst(str_replace('_', ' ', $mAvg->exam_type)) }}</td>
                                        <td><span class="badge bg-primary h5 mb-0">{{ number_format($mAvg->average, 2) }}%</span></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $mAvg->average }}%" aria-valuenow="{{ $mAvg->average }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
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

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line me-2"></i>Department-wise Student-Faculty Analysis</h6>
                <span class="badge bg-info text-dark px-3 py-2 rounded-pill"><i class="fas fa-info-circle me-1"></i> Ratio Report</span>
            </div>
            <div class="card-body">
                @if($departmentsAnalysis->isEmpty())
                    <div class="text-center py-4 text-muted">
                        No departments registered to map student-faculty ratio analysis.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Department Name (Code)</th>
                                    <th>Total Enrolled Students</th>
                                    <th>Total Assigned Faculty</th>
                                    <th>Student to Faculty Ratio</th>
                                    <th>Status / Workload Health</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departmentsAnalysis as $dept)
                                    @php
                                        $studentsCount = $dept->students_count;
                                        $facultyCount = $dept->faculty_count;
                                        
                                        // Compute ratio
                                        if ($facultyCount > 0) {
                                            $ratioValue = round($studentsCount / $facultyCount, 1);
                                            $ratioText = $ratioValue . " : 1";
                                        } else {
                                            $ratioValue = $studentsCount;
                                            $ratioText = $studentsCount > 0 ? "No Faculty" : "0 : 0";
                                        }
                                        
                                        // Determine health status
                                        if ($facultyCount == 0 && $studentsCount > 0) {
                                            $badgeClass = 'bg-danger';
                                            $healthText = 'Critical (No Faculty)';
                                        } elseif ($studentsCount == 0) {
                                            $badgeClass = 'bg-secondary';
                                            $healthText = 'No Students';
                                        } elseif ($ratioValue > 30) {
                                            $badgeClass = 'bg-warning text-dark';
                                            $healthText = 'High Workload';
                                        } elseif ($ratioValue < 10) {
                                            $badgeClass = 'bg-info text-dark';
                                            $healthText = 'Under-utilized / Small Batches';
                                        } else {
                                            $badgeClass = 'bg-success';
                                            $healthText = 'Optimal';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="fw-bold">{{ $dept->name }} ({{ $dept->code }})</td>
                                        <td>
                                            <span class="badge bg-primary px-3 py-2"><i class="fas fa-user-graduate me-1"></i> {{ $studentsCount }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success px-3 py-2"><i class="fas fa-chalkboard-teacher me-1"></i> {{ $facultyCount }}</span>
                                        </td>
                                        <td class="fw-bold fs-6 text-indigo">{{ $ratioText }}</td>
                                        <td>
                                            <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill">{{ $healthText }}</span>
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

@section('scripts')
<script>
    $(document).ready(function() {
        // Fees Chart (Donut)
        var feesCtx = document.getElementById('feesChart').getContext('2d');
        new Chart(feesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Fees Collected (INR)', 'Pending Dues (INR)'],
                datasets: [{
                    data: [{{ $paidFees }}, {{ $unpaidFees }}],
                    backgroundColor: ['#2af598', '#ff0844'],
                    hoverBackgroundColor: ['#17df7b', '#e20436'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Leaves Chart (Bar)
        var leavesCtx = document.getElementById('leavesChart').getContext('2d');
        new Chart(leavesCtx, {
            type: 'bar',
            data: {
                labels: ['Pending', 'Approved', 'Rejected'],
                datasets: [{
                    label: 'Leave Applications',
                    data: [
                        {{ $leaveStats['pending'] }}, 
                        {{ $leaveStats['approved'] }}, 
                        {{ $leaveStats['rejected'] }}
                    ],
                    backgroundColor: ['#f6d365', '#2af598', '#ff0844'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
