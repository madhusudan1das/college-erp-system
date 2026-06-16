<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    protected $table = 'exam_questions';
    public $timestamps = false;

    protected $fillable = [
        'exam_id', 'question_text', 'option_a', 
        'option_b', 'option_c', 'option_d', 'correct_option'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }
}
