<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerSheetUpload extends Model
{
    protected $table = 'answer_sheet_uploads';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'file_path', 'original_filename', 'mime_type', 'uploaded_by',
        'detected_subject', 'detected_subject_code', 'detected_department',
        'detected_exam_type', 'detected_semester', 'detected_student_info',
        'detected_date', 'subject_id', 'status', 'ai_confidence_score',
        'ai_raw_response', 'ai_source', 'error_message'
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function evaluatorAssignment()
    {
        return $this->hasOne(EvaluatorAssignment::class, 'answer_sheet_id');
    }
}
