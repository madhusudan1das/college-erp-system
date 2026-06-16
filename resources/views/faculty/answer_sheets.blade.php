@extends('layouts.app')

@section('title', 'Answer Sheets Auto-Allocation')

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
        color: #fff !important;
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
    .btn-aqua:active {
        transform: translateY(0);
    }
    .ai-import-panel {
        border: 2px dashed rgba(0, 188, 212, 0.3);
        border-radius: 12px;
        background-color: rgba(0, 188, 212, 0.02);
        padding: 40px 20px;
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
    .status-assigned { background-color: #d4edda; color: #155724; }
    .status-review_needed { background-color: #f8d7da; color: #721c24; }
    .status-failed { background-color: #e2e3e5; color: #383d41; }
    
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
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-file-alt text-aqua me-2"></i>Answer Sheets Auto-Allocation</h2>
        <p class="text-muted mb-0">Upload exam answer sheets to automatically extract metadata and allocate to matching evaluators via AI.</p>
    </div>
</div>

<div class="row">
    <!-- Left Column: Upload -->
    <div class="col-lg-4 mb-4">
        <div class="card premium-card border-0">
            <div class="card-header bg-transparent py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-cloud-upload-alt text-aqua me-2"></i>Upload Answer Sheet</h5>
            </div>
            <div class="card-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="ai-import-panel mb-3" id="dropZone">
                        <i class="fas fa-file-pdf text-aqua fa-3x mb-3 pulse-animation"></i>
                        <h6 class="fw-bold">Drag and drop file here</h6>
                        <span class="text-muted d-block mb-3 fs-7">Supports PDF, PNG, JPG, JPEG (Max 20MB)</span>
                        <button type="button" class="btn btn-outline-info btn-sm text-aqua border-aqua" onclick="document.getElementById('fileInput').click();">Browse Files</button>
                        <input type="file" id="fileInput" name="answer_sheet" accept=".pdf,.png,.jpg,.jpeg" style="display: none;">
                    </div>
                    
                    <div id="fileDetails" class="alert alert-info py-2 px-3 d-none mb-3">
                        <div class="d-flex justify-content-between align-items-center fs-7">
                            <span id="fileName" class="text-truncate me-2 fw-medium">filename.pdf</span>
                            <button type="button" class="btn-close" onclick="resetUploadForm()"></button>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn" class="btn btn-aqua w-100 py-2 d-none">
                        <i class="fas fa-robot me-1"></i> Run AI Processing
                    </button>
                </form>

                <!-- Processing UI -->
                <div id="processingUi" class="text-center py-4 d-none">
                    <div class="loading-spinner mx-auto mb-3" style="display: block;"></div>
                    <h6 class="fw-bold text-aqua">Gemini AI is processing document...</h6>
                    <p class="text-muted fs-7 mb-0">Running OCR, extracting subjects, exam details, and matching expertise workload for allocation.</p>
                </div>

                <!-- Success Results UI -->
                <div id="resultsUi" class="mt-4 p-3 rounded d-none" style="background-color: rgba(0, 188, 212, 0.05); border: 1px solid rgba(0, 188, 212, 0.15);">
                    <h6 class="fw-bold text-aqua border-bottom pb-2 mb-3"><i class="fas fa-magic me-1"></i> Extraction Results</h6>
                    <table class="table table-sm table-borderless fs-7 mb-3 text-dark">
                        <tr>
                            <td class="fw-bold text-muted" style="width: 40%;">Subject Name:</td>
                            <td id="resSubjectName">N/A</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Subject Code:</td>
                            <td id="resSubjectCode">N/A</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Department:</td>
                            <td id="resDepartment">N/A</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Exam Type:</td>
                            <td id="resExamType">N/A</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Semester:</td>
                            <td id="resSemester">N/A</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Student:</td>
                            <td id="resStudent">N/A</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Confidence:</td>
                            <td><span class="badge bg-aqua" id="resConfidence">0%</span></td>
                        </tr>
                    </table>
                    
                    <div class="p-3 bg-white border rounded border-success d-flex align-items-start">
                        <i class="fas fa-check-circle text-success fs-4 me-3 mt-1"></i>
                        <div>
                            <div class="fw-bold text-dark fs-7">Allocated Evaluator</div>
                            <div class="fw-semibold text-aqua fs-7" id="resEvaluatorName">Dr. Assigned Faculty</div>
                            <div class="text-muted fs-8" id="resReason">Matched subject expertise with lowest workload.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: List of Uploads & Assigned Evaluations -->
    <div class="col-lg-8">
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active bg-aqua border-0 px-4 py-2 me-2 shadow-sm rounded-pill fw-medium" id="pills-uploads-tab" data-bs-toggle="pill" data-bs-target="#pills-uploads" type="button" role="tab">My Uploads</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-aqua bg-transparent border-0 px-4 py-2 shadow-sm rounded-pill fw-medium" id="pills-evaluations-tab" data-bs-toggle="pill" data-bs-target="#pills-evaluations" type="button" role="tab">Assigned Evaluations</button>
            </li>
        </ul>
        
        <div class="tab-content" id="pills-tabContent">
            <!-- Tab 1: My Uploaded Files -->
            <div class="tab-pane fade show active" id="pills-uploads" role="tabpanel">
                <div class="card premium-card border-0">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover datatable align-middle">
                                <thead>
                                    <tr>
                                        <th>File</th>
                                        <th>Subject</th>
                                        <th>Semester</th>
                                        <th>Status</th>
                                        <th>Evaluator</th>
                                        <th>Score</th>
                                        <th>Uploaded</th>
                                    </tr>
                                </thead>
                                <tbody id="uploadsTableBody">
                                    @foreach($uploads as $upload)
                                        <tr>
                                            <td>
                                                <a href="{{ asset($upload->file_path) }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                                                    <i class="fas fa-file-pdf text-danger me-2"></i>{{ Str::limit($upload->original_filename, 20) }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($upload->subject)
                                                    <span class="fw-semibold">{{ $upload->subject->name }}</span>
                                                    <div class="text-muted fs-8">{{ $upload->subject->code }}</div>
                                                @else
                                                    <span class="text-muted fs-7">Unresolved</span>
                                                    <div class="text-muted fs-8">AI: {{ $upload->detected_subject }}</div>
                                                @endif
                                            </td>
                                            <td>{{ $upload->detected_semester ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge-status status-{{ $upload->status }}">
                                                    {{ ucfirst($upload->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($upload->evaluatorAssignment)
                                                    <span class="fw-semibold text-aqua">{{ $upload->evaluatorAssignment->evaluator->first_name }} {{ $upload->evaluatorAssignment->evaluator->last_name }}</span>
                                                @else
                                                    <span class="text-muted">None</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($upload->ai_confidence_score !== null)
                                                    <span class="badge bg-aqua">{{ intval($upload->ai_confidence_score) }}%</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
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

            <!-- Tab 2: Assigned to me to evaluate -->
            <div class="tab-pane fade" id="pills-evaluations" role="tabpanel">
                <div class="card premium-card border-0">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover datatable align-middle">
                                <thead>
                                    <tr>
                                        <th>Answer Sheet File</th>
                                        <th>Subject</th>
                                        <th>Uploader</th>
                                        <th>AI Reason</th>
                                        <th>Assigned At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $assign)
                                        <tr>
                                            <td>
                                                <a href="{{ asset($assign->answerSheet->file_path) }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                                                    <i class="fas fa-file-pdf text-danger me-2"></i>{{ Str::limit($assign->answerSheet->original_filename, 25) }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="fw-semibold">{{ $assign->answerSheet->subject->name ?? 'N/A' }}</span>
                                                <div class="text-muted fs-8">{{ $assign->answerSheet->subject->code ?? '' }}</div>
                                            </td>
                                            <td>
                                                <span class="fs-7 text-dark">{{ $assign->answerSheet->uploader->username }}</span>
                                            </td>
                                            <td>
                                                <span class="fs-8 text-muted d-inline-block text-wrap" style="max-width: 200px;">{{ $assign->assignment_reason }}</span>
                                            </td>
                                            <td>
                                                <span class="fs-8 text-muted">{{ \Carbon\Carbon::parse($assign->assigned_at)->format('M d, H:i') }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ asset($assign->answerSheet->file_path) }}" target="_blank" class="btn btn-outline-info border-aqua text-aqua btn-sm">
                                                    <i class="fas fa-eye me-1"></i> View & Evaluate
                                                </a>
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
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var dropZone = $('#dropZone');
        var fileInput = $('#fileInput');

        // Toggle pills style on click
        $('#pills-tab button').click(function(e) {
            e.preventDefault();
            $('#pills-tab button').removeClass('bg-aqua text-white').addClass('text-aqua bg-transparent');
            $(this).addClass('bg-aqua text-white').removeClass('text-aqua bg-transparent');
        });

        // Drag & Drop event handlers
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

        // Form Submit AJAX
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            
            $('#submitBtn').addClass('d-none');
            $('#dropZone').addClass('d-none');
            $('#fileDetails').addClass('d-none');
            $('#processingUi').removeClass('d-none');

            $.ajax({
                url: "{{ route('faculty.answer-sheets.upload') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#processingUi').addClass('d-none');
                    $('#dropZone').removeClass('d-none');
                    resetUploadForm();

                    if (response.success) {
                        var data = response.data.upload;
                        
                        // Populate results
                        $('#resSubjectName').text(data.detected_subject || 'Unresolved');
                        $('#resSubjectCode').text(data.detected_subject_code || 'Unresolved');
                        $('#resDepartment').text(data.detected_department || 'Unresolved');
                        $('#resExamType').text(data.detected_exam_type || 'Unresolved');
                        $('#resSemester').text(data.detected_semester || 'Unresolved');
                        $('#resStudent').text(data.detected_student_info || 'Unresolved');
                        $('#resConfidence').text((data.ai_confidence_score || 0) + '%');
                        
                        if (response.status === 'assigned') {
                            $('#resEvaluatorName').text(response.data.evaluator_name);
                            $('#resReason').text(response.data.assignment.assignment_reason);
                            $('#resultsUi').removeClass('d-none');
                        } else {
                            $('#resEvaluatorName').text('None - Review Needed');
                            $('#resReason').text(data.error_message || 'Fuzzy matching could not assign evaluator automatically.');
                            $('#resultsUi').removeClass('d-none');
                        }

                        // Prepend row to table
                        var newRow = `
                            <tr>
                                <td>
                                    <a href="/${data.file_path}" target="_blank" class="fw-bold text-dark text-decoration-none">
                                        <i class="fas fa-file-pdf text-danger me-2"></i>${data.original_filename}
                                    </a>
                                </td>
                                <td>
                                    <span class="fw-semibold">${data.detected_subject || 'Unresolved'}</span>
                                    <div class="text-muted fs-8">${data.detected_subject_code || ''}</div>
                                </td>
                                <td>${data.detected_semester || 'N/A'}</td>
                                <td>
                                    <span class="badge-status status-${data.status}">
                                        ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-aqua">${response.data.evaluator_name || 'None'}</span>
                                </td>
                                <td>
                                    <span class="badge bg-aqua">${parseInt(data.ai_confidence_score || 0)}%</span>
                                </td>
                                <td>
                                    <span class="fs-8 text-muted">Just now</span>
                                </td>
                            </tr>
                        `;
                        $('#uploadsTableBody').prepend(newRow);
                    }
                },
                error: function(xhr) {
                    $('#processingUi').addClass('d-none');
                    $('#dropZone').removeClass('d-none');
                    resetUploadForm();
                    
                    var errMsg = 'An error occurred during file upload.';
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
