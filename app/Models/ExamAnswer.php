<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    protected $table = 'exam_answers';
    public $timestamps = false;

    protected $fillable = ['attempt_id', 'question_id', 'selected_option', 'is_correct'];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id');
    }
}
