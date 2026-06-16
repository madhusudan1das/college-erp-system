@extends('layouts.app')

@section('title', 'AI Timetable OCR & Auto-Allocation')

@section('styles')
<style>
    .premium-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 188, 212, 0.15);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    .premium-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(0, 188, 212, 0.1);
    }
    .text-aqua {
        color: #00bcd4 !important;
    }
    .bg-aqua {
        background-color: #00bcd4 !important;
        color: #white !important;
    }
    .btn-aqua {
        background: linear-gradient(135deg, #00bcd4 0%, #00acc1 100%);
        color: #white;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 188, 212, 0.3);
        transition: all 0.3s ease;
        color: #fff !important;
        font-weight: 500;
    }
    .btn-aqua:hover {
        background: linear-gradient(135deg, #00acc1 0%, #0097a7 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(0, 188, 212, 0.4);
    }
    .ai-import-panel {
        border: 2px dashed rgba(0, 188, 212, 0.3);
        border-radius: 12px;
        background-color: rgba(0, 188, 212, 0.02);
        padding: 45px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .ai-import-panel:hover, .ai-import-panel.dragover {
        background-color: rgba(0, 188, 212, 0.06);
        border-color: #00bcd4;
    }
    .badge-status {
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .status-pending { background-color: #ffeeb3; color: #856404; }
    .status-processing { background-color: #cce5ff; color: #004085; animation: pulse 1.5s infinite; }
    .status-processed { background-color: #d4edda; color: #155724; }
    .status-partial { background-color: #e2f0d9; color: #385723; }
    .status-failed { background-color: #f8d7da; color: #721c24; }
    
    @keyframes pulse {
        0% { opacity: 0.6; }
        50% { opacity: 1; }
        100% { opacity: 0.6; }
    }
    .loading-spinner {
        display: none;
        width: 3rem;
        height: 3rem;
        border: 0.25em solid rgba(0, 188, 212, 0.2);
        border-right-color: #00bcd4;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .pulse-animation {
        animation: heartBeat 1.5s ease-in-out infinite;
    }
    @keyframes heartBeat {
        0% { transform: scale(1); }
        14% { transform: scale(1.1); }
        28% { transform: scale(1); }
        42% { transform: scale(1.1); }
        70% { transform: scale(1); }
    }
    .stat-box {
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-calendar-alt text-aqua me-2"></i>AI Timetable OCR & Auto-Allocation</h2>
        <p class="text-muted mb-0">Upload a timetable schedule (PDF/Image) to parse and automatically create conflict-free schedule entries via AI.</p>
    </div>
</div>

<div class="row">
    <!-- Left Column: Upload -->
    <div class="col-lg-5 mb-4">
        <div class="card premium-card border-0">
            <div class="card-header bg-transparent py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-cloud-upload-alt text-aqua me-2"></i>Upload Timetable Sheet</h5>
            </div>
            <div class="card-body">
                <form id="timetableForm" enctype="multipart/form-data">
                    @csrf
                    <div class="ai-import-panel mb-3" id="dropZone">
                        <i class="fas fa-calendar-check text-aqua fa-3x mb-3 pulse-animation"></i>
                        <h6 class="fw-bold">Drag and drop weekly schedule file here</h6>
                        <span class="text-muted d-block mb-3 fs-7">Supports PDF, PNG, JPG, JPEG (Max 20MB)</span>
                        <button type="button" class="btn btn-outline-info btn-sm text-aqua border-aqua" onclick="document.getElementById('fileInput').click();">Browse Files</button>
                        <input type="file" id="fileInput" name="timetable_file" accept=".pdf,.png,.jpg,.jpeg" style="display: none;">
                    </div>
                    
                    <div id="fileDetails" class="alert alert-info py-2 px-3 d-none mb-3">
                        <div class="d-flex justify-content-between align-items-center fs-7">
                            <span id="fileName" class="text-truncate me-2 fw-medium">filename.pdf</span>
                            <button type="button" class="btn-close" onclick="resetUploadForm()"></button>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn" class="btn btn-aqua w-100 py-2 d-none">
                        <i class="fas fa-robot me-1"></i> Start Timetable Allocation
                    </button>
                </form>

                <!-- Processing UI -->
                <div id="processingUi" class="text-center py-4 d-none">
                    <div class="loading-spinner mx-auto mb-3" style="display: block;"></div>
                    <h6 class="fw-bold text-aqua">Gemini AI is parsing timetable layout...</h6>
                    <p class="text-muted fs-7 mb-0">Performing OCR, reading days, timeslots, faculty names, subject codes, rooms, and performing conflict resolution checks.</p>
                </div>
            </div>
        </div>

        <!-- Latest Process Results Summary Card -->
        <div id="resultsUi" class="card premium-card border-0 mt-4 d-none">
            <div class="card-header bg-transparent py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-aqua"><i class="fas fa-info-circle me-1"></i> Allocation Summary</h5>
            </div>
            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="stat-box bg-success-light" style="background-color: rgba(40, 167, 69, 0.08);">
                            <div class="h3 fw-bold text-success mb-0" id="statCreated">0</div>
                            <div class="text-muted fs-8">Slots Created</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box bg-secondary-light" style="background-color: rgba(108, 117, 125, 0.08);">
                            <div class="h3 fw-bold text-secondary mb-0" id="statSkipped">0</div>
                            <div class="text-muted fs-8">Skipped (Duplicates)</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box bg-danger-light" style="background-color: rgba(220, 53, 69, 0.08);">
                            <div class="h3 fw-bold text-danger mb-0" id="statConflicts">0</div>
                            <div class="text-muted fs-8">Conflicts Found</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box bg-warning-light" style="background-color: rgba(255, 193, 7, 0.08);">
                            <div class="h3 fw-bold text-warning mb-0" id="statUnmatched">0</div>
                            <div class="text-muted fs-8">Unmatched Entries</div>
                        </div>
                    </div>
                </div>

                <!-- Conflicts Report Panel -->
                <div id="conflictPanel" class="d-none mb-3">
                    <h6 class="fw-bold text-danger border-bottom pb-1 mb-2 fs-7"><i class="fas fa-exclamation-triangle"></i> Detected Conflicts (Skipped)</h6>
                    <ul class="list-group list-group-flush fs-8 text-danger" id="conflictList" style="max-height: 150px; overflow-y: auto;">
                        <!-- conflicts -->
                    </ul>
                </div>

                <!-- Unmatched/Review Panel -->
                <div id="unmatchedPanel" class="d-none">
                    <h6 class="fw-bold text-warning border-bottom pb-1 mb-2 fs-7"><i class="fas fa-search"></i> Unmatched / Needs Review (Skipped)</h6>
                    <ul class="list-group list-group-flush fs-8 text-warning" id="unmatchedList" style="max-height: 150px; overflow-y: auto;">
                        <!-- unmatched -->
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: History -->
    <div class="col-lg-7">
        <div class="card premium-card border-0">
            <div class="card-header bg-transparent py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-history text-aqua me-2"></i>Timetable Upload History</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover datatable align-middle">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Conflicts</th>
                                <th>Unmatched</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            @foreach($uploads as $upload)
                                <tr>
                                    <td>
                                        <a href="{{ asset($upload->file_path) }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                                            <i class="fas fa-file-invoice text-info me-2"></i>{{ Str::limit($upload->original_filename, 20) }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge-status status-{{ $upload->status }}">
                                            {{ ucfirst($upload->status) }}
                                        </span>
                                    </td>
                                    <td><span class="badge bg-success">{{ $upload->slots_created }}</span></td>
                                    <td><span class="badge bg-danger">{{ $upload->conflicts_found }}</span></td>
                                    <td><span class="badge bg-warning text-dark">{{ $upload->unmatched_entries }}</span></td>
                                    <td>
                                        <span class="fs-8 text-muted">{{ \Carbon\Carbon::parse($upload->created_at)->format('M d, H:i') }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var dropZone = $('#dropZone');
        var fileInput = $('#fileInput');

        // Drag & Drop
        dropZone.on('dragover', function(e) {
            e.preventDefault();
            dropZone.addClass('dragover');
        });

        dropZone.on('dragleave', function(e) {
            e.preventDefault();
            dropZone.removeClass('dragover');
        });

        dropZone.on('drop', function(e) {
            e.preventDefault();
            dropZone.removeClass('dragover');
            var files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                fileInput[0].files = files;
                handleFileSelect(files[0]);
            }
        });

        fileInput.on('change', function() {
            var files = fileInput[0].files;
            if (files.length > 0) {
                handleFileSelect(files[0]);
            }
        });

        function handleFileSelect(file) {
            $('#fileName').text(file.name);
            $('#fileDetails').removeClass('d-none');
            $('#submitBtn').removeClass('d-none');
            $('#resultsUi').addClass('d-none');
        }

        // AJAX Upload
        $('#timetableForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $('#submitBtn').addClass('d-none');
            $('#dropZone').addClass('d-none');
            $('#fileDetails').addClass('d-none');
            $('#processingUi').removeClass('d-none');

            $.ajax({
                url: "{{ route('admin.ai-timetable.process') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#processingUi').addClass('d-none');
                    $('#dropZone').removeClass('d-none');
                    resetUploadForm();

                    if (response.success) {
                        var upload = response.data.upload;
                        
                        // Populate stats
                        $('#statCreated').text(upload.slots_created);
                        $('#statSkipped').text(upload.slots_skipped);
                        $('#statConflicts').text(upload.conflicts_found);
                        $('#statUnmatched').text(upload.unmatched_entries);
                        
                        // Populate conflicts
                        $('#conflictList').empty();
                        if (response.data.conflicts.length > 0) {
                            response.data.conflicts.forEach(function(c) {
                                $('#conflictList').append(`
                                    <li class="list-group-item bg-transparent py-1 border-0">
                                        <i class="fas fa-times-circle me-1"></i><b>${c.slot.day_of_week} ${c.slot.start_time}-${c.slot.end_time} (${c.slot.subject_name || 'Subject'}):</b> ${c.reason}
                                    </li>
                                `);
                            });
                            $('#conflictPanel').removeClass('d-none');
                        } else {
                            $('#conflictPanel').addClass('d-none');
                        }

                        // Populate unmatched
                        $('#unmatchedList').empty();
                        if (response.data.unmatched.length > 0) {
                            response.data.unmatched.forEach(function(u) {
                                $('#unmatchedList').append(`
                                    <li class="list-group-item bg-transparent py-1 border-0">
                                        <i class="fas fa-exclamation-triangle me-1"></i><b>${u.slot.day_of_week} ${u.slot.start_time}-${u.slot.end_time}:</b> ${u.reason}
                                    </li>
                                `);
                            });
                            $('#unmatchedPanel').removeClass('d-none');
                        } else {
                            $('#unmatchedPanel').addClass('d-none');
                        }

                        $('#resultsUi').removeClass('d-none');

                        // Prepend row to table
                        var newRow = `
                            <tr>
                                <td>
                                    <a href="/${upload.file_path}" target="_blank" class="fw-bold text-dark text-decoration-none">
                                        <i class="fas fa-file-invoice text-info me-2"></i>${upload.original_filename}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge-status status-${upload.status}">
                                        ${upload.status.charAt(0).toUpperCase() + upload.status.slice(1)}
                                    </span>
                                </td>
                                <td><span class="badge bg-success">${upload.slots_created}</span></td>
                                <td><span class="badge bg-danger">${upload.conflicts_found}</span></td>
                                <td><span class="badge bg-warning text-dark">${upload.unmatched_entries}</span></td>
                                <td>
                                    <span class="fs-8 text-muted">Just now</span>
                                </td>
                            </tr>
                        `;
                        $('#historyTableBody').prepend(newRow);
                    }
                },
                error: function(xhr) {
                    $('#processingUi').addClass('d-none');
                    $('#dropZone').removeClass('d-none');
                    resetUploadForm();

                    var errMsg = 'An error occurred during timetable processing.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    alert(errMsg);
                }
            });
        });
    });

    function resetUploadForm() {
        $('#fileInput').val('');
        $('#fileDetails').addClass('d-none');
        $('#submitBtn').addClass('d-none');
    }
</script>
@endsection
