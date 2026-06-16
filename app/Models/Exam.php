<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $table = 'exams';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = ['subject_id', 'faculty_id', 'title', 'duration_minutes', 'status'];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class, 'exam_id');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class, 'exam_id');
    }
}
