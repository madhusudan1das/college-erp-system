<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    protected $table = 'exam_attempts';
    public $timestamps = false;

    protected $fillable = [
        'exam_id', 'student_id', 'start_time', 
        'end_time', 'score', 'total_questions', 'status'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'attempt_id');
    }
}
