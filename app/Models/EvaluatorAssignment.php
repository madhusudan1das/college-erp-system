<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluatorAssignment extends Model
{
    protected $table = 'evaluator_assignments';
    public $timestamps = false;

    protected $fillable = [
        'answer_sheet_id', 'evaluator_faculty_id', 'assigned_by_ai',
        'assignment_reason', 'assignment_score', 'status',
        'assigned_at', 'completed_at'
    ];

    public function answerSheet()
    {
        return $this->belongsTo(AnswerSheetUpload::class, 'answer_sheet_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(Faculty::class, 'evaluator_faculty_id');
    }
}
