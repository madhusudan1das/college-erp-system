@extends('layouts.app')

@section('title', 'Manage Exam Questions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Questions: {{ $exam->title }}</h2>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="collapse" data-bs-target="#aiImportCollapse">
            <i class="fas fa-wand-magic-sparkles me-2"></i>Import with AI
        </button>
        <a href="{{ route('faculty.exams') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Exams</a>
    </div>
</div>

<!-- AI Question Parser Collapsible Card -->
<div class="collapse mb-4" id="aiImportCollapse">
    <div class="premium-card shadow border-start border-primary" style="border-left-width: 5px !important;">
        <div class="card-header bg-light py-3 border-bottom border-light d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-robot me-2"></i>AI Question Assistant</h6>
            <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#aiImportCollapse" aria-label="Close"></button>
        </div>
        <div class="card-body p-4">
            <!-- Tabs Navigation -->
            <ul class="nav nav-pills mb-4" id="aiTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="upload-tab" data-bs-toggle="pill" data-bs-target="#tab-upload" type="button" role="tab" aria-controls="tab-upload" aria-selected="true">
                        <i class="fas fa-file-upload me-2"></i>Upload Exam Paper File
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="topic-tab" data-bs-toggle="pill" data-bs-target="#tab-topic" type="button" role="tab" aria-controls="tab-topic" aria-selected="false">
                        <i class="fas fa-keyboard me-2"></i>Generate by Topic / Section
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="aiTabContent">
                <!-- Tab 1: Upload Panel -->
                <div class="tab-pane fade show active" id="tab-upload" role="tabpanel" aria-labelledby="upload-tab">
                    <div id="aiUploadArea" class="ai-import-panel p-4 text-center border rounded bg-light">
                        <i class="fas fa-file-invoice fa-3x text-primary ai-pulse-element mb-3" id="aiUploadIcon" style="padding: 15px; background: rgba(99, 102, 241, 0.1); border-radius: 50%;"></i>
                        <h5 class="fw-semibold">Upload Exam Paper</h5>
                        <p class="text-muted small">Select a PDF question paper or a clear image (PNG, JPG, JPEG)</p>
                        <div class="d-flex justify-content-center mt-3">
                            <input type="file" id="aiQuestionFile" class="form-control w-50" accept="application/pdf,image/*">
                        </div>
                        <button type="button" id="btnParseAI" class="btn btn-primary mt-3 px-4 py-2"><i class="fas fa-wand-magic-sparkles me-2"></i>Analyze and Parse</button>
                    </div>
                </div>

                <!-- Tab 2: Generate by Topic Panel -->
                <div class="tab-pane fade" id="tab-topic" role="tabpanel" aria-labelledby="topic-tab">
                    <div id="aiTopicArea" class="ai-import-panel p-4 border rounded bg-light">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="aiTopicInput" class="form-label fw-semibold">Syllabus Section / Topic / Keywords</label>
                                <textarea id="aiTopicInput" class="form-control" rows="2" placeholder="e.g. OOP inheritance and polymorphism, SQL Joins and Normalization, HTML5 canvas element..."></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="aiQuestionCount" class="form-label fw-semibold">Number of Questions</label>
                                <select id="aiQuestionCount" class="form-select">
                                    <option value="5">5 Questions</option>
                                    <option value="10" selected>10 Questions</option>
                                    <option value="15">15 Questions</option>
                                    <option value="20">20 Questions</option>
                                </select>
                            </div>
                        </div>
                        <button type="button" id="btnGenerateTopicAI" class="btn btn-primary mt-3 px-4 py-2"><i class="fas fa-wand-magic-sparkles me-2"></i>Generate with AI</button>
                    </div>
                </div>
            </div>

            <!-- Loading Spinner State -->
            <div id="aiLoadingArea" class="text-center py-5 d-none">
                <div class="position-relative d-inline-block mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-cog fa-3x text-primary ai-spin" style="line-height: 80px;"></i>
                    <i class="fas fa-robot position-absolute start-50 top-50 translate-middle text-success fa-lg"></i>
                </div>
                <h5 class="fw-semibold mt-2">AI is structuring your questions...</h5>
                <p class="text-muted small">Generating/parsing text, identifying options, and formatting options.</p>
                <div class="progress w-50 mx-auto mt-3" style="height: 6px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 100%"></div>
                </div>
            </div>

            <!-- Verification & Custom Choice Selection Table -->
            <div id="aiResultsArea" class="d-none mt-4">
                <div class="alert alert-info py-2 d-none" id="aiWarningAlert">
                    <i class="fas fa-exclamation-triangle me-2"></i><span id="aiWarningText"></span>
                </div>
                
                <h6 class="fw-semibold text-dark mb-3"><i class="fas fa-clipboard-check me-2"></i>Verify and Import Questions</h6>
                <div class="table-responsive">
                    <table class="table table-hover border">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">
                                    <input type="checkbox" id="selectAllQuestions" checked style="transform: scale(1.2); cursor: pointer;">
                                </th>
                                <th width="40%">Question Text</th>
                                <th width="40%">Options</th>
                                <th width="15%">Correct Option</th>
                            </tr>
                        </thead>
                        <tbody id="aiQuestionsTbody">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <button type="button" id="btnResetAI" class="btn btn-outline-secondary"><i class="fas fa-undo me-1"></i> Start Over</button>
                    <button type="button" id="btnImportAI" class="btn btn-success px-4 py-2"><i class="fas fa-cloud-upload-alt me-1"></i> Import Selected (<span id="aiImportCount">0</span>)</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Add Question Form -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-primary">Add New Question</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('faculty.exams.questions.store', $exam->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Question Text</label>
                        <textarea name="question_text" class="form-control" rows="3" required placeholder="Write question here..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Option A</label>
                        <input type="text" name="option_a" class="form-control" required placeholder="Option A">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Option B</label>
                        <input type="text" name="option_b" class="form-control" required placeholder="Option B">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Option C</label>
                        <input type="text" name="option_c" class="form-control" required placeholder="Option C">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Option D</label>
                        <input type="text" name="option_d" class="form-control" required placeholder="Option D">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correct Option</label>
                        <select name="correct_option" class="form-control" required>
                            <option value="A">Option A</option>
                            <option value="B">Option B</option>
                            <option value="C">Option C</option>
                            <option value="D">Option D</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Question</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Question List -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Question List</h6>
            </div>
            <div class="card-body">
                @if ($exam->questions->isEmpty())
                    <div class="alert alert-info text-center py-4">No questions added yet. Use the form on the left to add questions.</div>
                @else
                    @foreach ($exam->questions as $index => $q)
                        <div class="card shadow-sm border-left-primary mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="font-weight-bold text-primary mb-2" style="max-width: 85%;">Q{{ $index + 1 }}: {{ $q->question_text }}</h6>
                                    <div class="d-flex gap-2 align-items-center">
                                        <button type="button" class="btn btn-sm btn-outline-info btn-edit-question"
                                                data-id="{{ $q->id }}"
                                                data-text="{{ $q->question_text }}"
                                                data-a="{{ $q->option_a }}"
                                                data-b="{{ $q->option_b }}"
                                                data-c="{{ $q->option_c }}"
                                                data-d="{{ $q->option_d }}"
                                                data-correct="{{ $q->correct_option }}"
                                                title="Edit Question">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('faculty.exams.questions.delete', $q->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this question?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Question"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                                <div class="row small mt-2">
                                    <div class="col-md-6 mb-1 {{ $q->correct_option == 'A' ? 'text-success font-weight-bold' : 'text-muted' }}">
                                        <strong>A:</strong> {{ $q->option_a }}
                                    </div>
                                    <div class="col-md-6 mb-1 {{ $q->correct_option == 'B' ? 'text-success font-weight-bold' : 'text-muted' }}">
                                        <strong>B:</strong> {{ $q->option_b }}
                                    </div>
                                    <div class="col-md-6 mb-1 {{ $q->correct_option == 'C' ? 'text-success font-weight-bold' : 'text-muted' }}">
                                        <strong>C:</strong> {{ $q->option_c }}
                                    </div>
                                    <div class="col-md-6 mb-1 {{ $q->correct_option == 'D' ? 'text-success font-weight-bold' : 'text-muted' }}">
                                        <strong>D:</strong> {{ $q->option_d }}
                                    </div>
                                </div>
                                <div class="mt-2 text-end">
                                    <span class="badge bg-success">Correct Option: {{ $q->correct_option }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
<!-- Edit Question Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-labelledby="editQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editQuestionModalLabel"><i class="fas fa-edit text-primary me-2"></i>Edit Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editQuestionForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question Text</label>
                        <textarea name="question_text" id="edit_question_text" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Option A</label>
                            <input type="text" name="option_a" id="edit_option_a" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Option B</label>
                            <input type="text" name="option_b" id="edit_option_b" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Option C</label>
                            <input type="text" name="option_c" id="edit_option_c" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Option D</label>
                            <input type="text" name="option_d" id="edit_option_d" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correct Option</label>
                        <select name="correct_option" id="edit_correct_option" class="form-select" required>
                            <option value="A">Option A</option>
                            <option value="B">Option B</option>
                            <option value="C">Option C</option>
                            <option value="D">Option D</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let parsedQuestionsList = [];

    // Edit Question Trigger
    $('.btn-edit-question').on('click', function() {
        const id = $(this).data('id');
        const text = $(this).data('text');
        const a = $(this).data('a');
        const b = $(this).data('b');
        const c = $(this).data('c');
        const d = $(this).data('d');
        const correct = $(this).data('correct');

        // Populate form
        $('#edit_question_text').val(text);
        $('#edit_option_a').val(a);
        $('#edit_option_b').val(b);
        $('#edit_option_c').val(c);
        $('#edit_option_d').val(d);
        $('#edit_correct_option').val(correct);

        // Update form action URL
        $('#editQuestionForm').attr('action', `/faculty/admin/exams/questions/${id}`);
        // Wait, the route is faculty.exams.questions.update. The URL structure in web.php is:
        // Route::put('/exams/questions/{id}', [FacultyController::class, 'updateQuestion'])->name('exams.questions.update');
        // Let's verify it matches the route. The faculty prefix makes the URL `/faculty/exams/questions/{id}`.
        // Yes, let's use: `/faculty/exams/questions/${id}`
        $('#editQuestionForm').attr('action', `/faculty/exams/questions/${id}`);

        // Show modal
        $('#editQuestionModal').modal('show');
    });

    function renderQuestions(questions) {
        let tbodyHtml = '';
        questions.forEach(function(q, index) {
            tbodyHtml += `
            <tr class="ai-question-row" data-index="${index}">
                <td class="text-center align-middle">
                    <input type="checkbox" class="form-check-input select-q-import" checked style="transform: scale(1.2); cursor: pointer;">
                </td>
                <td>
                    <textarea class="form-control q-text" rows="2" style="font-size: 0.9rem; font-weight: 500;">${q.question_text || ''}</textarea>
                </td>
                <td>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light fw-semibold">A</span>
                                <input type="text" class="form-control q-opt-a" value="${q.option_a || ''}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light fw-semibold">B</span>
                                <input type="text" class="form-control q-opt-b" value="${q.option_b || ''}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light fw-semibold">C</span>
                                <input type="text" class="form-control q-opt-c" value="${q.option_c || ''}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light fw-semibold">D</span>
                                <input type="text" class="form-control q-opt-d" value="${q.option_d || ''}">
                            </div>
                        </div>
                    </div>
                </td>
                <td class="align-middle">
                    <select class="form-select form-select-sm q-correct">
                        <option value="A" ${q.correct_option === 'A' ? 'selected' : ''}>Option A</option>
                        <option value="B" ${q.correct_option === 'B' ? 'selected' : ''}>Option B</option>
                        <option value="C" ${q.correct_option === 'C' ? 'selected' : ''}>Option C</option>
                        <option value="D" ${q.correct_option === 'D' ? 'selected' : ''}>Option D</option>
                    </select>
                </td>
            </tr>
            `;
        });
        $('#aiQuestionsTbody').html(tbodyHtml);
        $('#aiResultsArea').removeClass('d-none');
        $('#selectAllQuestions').prop('checked', true);
        updateImportCount();
    }

    // Parse AI (Upload file)
    $('#btnParseAI').on('click', function() {
        const fileInput = $('#aiQuestionFile')[0];
        if (fileInput.files.length === 0) {
            alert('Please select a PDF or Image question paper first.');
            return;
        }

        const file = fileInput.files[0];
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        // Transition UI to loading state
        $('.ai-import-panel').addClass('d-none');
        $('#aiTab').addClass('d-none');
        $('#aiLoadingArea').removeClass('d-none');
        $('#aiResultsArea').addClass('d-none');

        $.ajax({
            url: "{{ route('faculty.exams.questions.parse', $exam->id) }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                $('#aiLoadingArea').addClass('d-none');
                $('#aiTab').removeClass('d-none');
                
                if (response.success && response.questions && response.questions.length > 0) {
                    parsedQuestionsList = response.questions;
                    
                    if (response.warning) {
                        $('#aiWarningText').text(response.warning);
                        $('#aiWarningAlert').removeClass('d-none');
                    } else {
                        $('#aiWarningAlert').addClass('d-none');
                    }

                    renderQuestions(parsedQuestionsList);
                } else {
                    alert('Could not find any structured questions in the document. Please try again with a clearer file.');
                    $('.ai-import-panel').removeClass('d-none');
                }
            },
            error: function(xhr, status, error) {
                $('#aiLoadingArea').addClass('d-none');
                $('#aiTab').removeClass('d-none');
                $('.ai-import-panel').removeClass('d-none');
                
                let errorMsg = 'Failed to analyze question paper. ';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg += xhr.responseJSON.message;
                } else {
                    errorMsg += error;
                }
                alert(errorMsg);
            }
        });
    });

    // Generate by Topic AI
    $('#btnGenerateTopicAI').on('click', function() {
        const topic = $('#aiTopicInput').val().trim();
        const count = $('#aiQuestionCount').val();

        if (!topic) {
            alert('Please enter a topic or section keywords first.');
            return;
        }

        // Transition UI to loading state
        $('.ai-import-panel').addClass('d-none');
        $('#aiTab').addClass('d-none');
        $('#aiLoadingArea').removeClass('d-none');
        $('#aiResultsArea').addClass('d-none');

        $.ajax({
            url: "{{ route('faculty.exams.questions.generate', $exam->id) }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                topic: topic,
                count: count
            },
            dataType: "json",
            success: function(response) {
                $('#aiLoadingArea').addClass('d-none');
                $('#aiTab').removeClass('d-none');
                
                if (response.success && response.questions && response.questions.length > 0) {
                    parsedQuestionsList = response.questions;
                    
                    if (response.warning) {
                        $('#aiWarningText').text(response.warning);
                        $('#aiWarningAlert').removeClass('d-none');
                    } else {
                        $('#aiWarningAlert').addClass('d-none');
                    }

                    renderQuestions(parsedQuestionsList);
                } else {
                    alert('Could not generate any questions. Please try again with a clearer topic.');
                    $('.ai-import-panel').removeClass('d-none');
                }
            },
            error: function(xhr, status, error) {
                $('#aiLoadingArea').addClass('d-none');
                $('#aiTab').removeClass('d-none');
                $('.ai-import-panel').removeClass('d-none');
                
                let errorMsg = 'Failed to generate questions. ';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg += xhr.responseJSON.message;
                } else {
                    errorMsg += error;
                }
                alert(errorMsg);
            }
        });
    });

    // Reset upload/topic panels
    $('#btnResetAI').on('click', function() {
        $('#aiQuestionFile').val('');
        $('#aiTopicInput').val('');
        $('.ai-import-panel').removeClass('d-none');
        $('#aiTab').removeClass('d-none');
        $('#aiResultsArea').addClass('d-none');
    });

    // Toggle Select All checkboxes
    $('#selectAllQuestions').on('change', function() {
        const checked = $(this).is(':checked');
        $('.select-q-import').prop('checked', checked);
        updateImportCount();
    });

    // Track checkbox selection count
    $(document).on('change', '.select-q-import', function() {
        updateImportCount();
    });

    function updateImportCount() {
        const total = $('.select-q-import').length;
        const checkedCount = $('.select-q-import:checked').length;
        $('#aiImportCount').text(checkedCount);
        $('#btnImportAI').prop('disabled', checkedCount === 0);
        $('#selectAllQuestions').prop('checked', total > 0 && total === checkedCount);
    }

    // Submit imported questions
    $('#btnImportAI').on('click', function() {
        const selectedQuestions = [];
        
        $('.ai-question-row').each(function() {
            const row = $(this);
            const isChecked = row.find('.select-q-import').is(':checked');
            
            if (isChecked) {
                selectedQuestions.push({
                    question_text: row.find('.q-text').val().trim(),
                    option_a: row.find('.q-opt-a').val().trim(),
                    option_b: row.find('.q-opt-b').val().trim(),
                    option_c: row.find('.q-opt-c').val().trim(),
                    option_d: row.find('.q-opt-d').val().trim(),
                    correct_option: row.find('.q-correct').val()
                });
            }
        });

        if (selectedQuestions.length === 0) {
            alert('Please select at least one question to import.');
            return;
        }

        const importBtn = $('#btnImportAI');
        importBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Importing...');

        $.ajax({
            url: "{{ route('faculty.exams.questions.import', $exam->id) }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                questions: selectedQuestions
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Import failed: ' + response.message);
                    importBtn.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt me-1"></i> Import Selected');
                }
            },
            error: function(xhr, status, error) {
                let errorMsg = 'Failed to import questions. ';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg += xhr.responseJSON.message;
                } else {
                    errorMsg += error;
                }
                alert(errorMsg);
                importBtn.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt me-1"></i> Import Selected');
            }
        });
    });
});
</script>
@endsection
