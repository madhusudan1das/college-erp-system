<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    protected $table = 'marks';
    public $timestamps = false;

    protected $fillable = ['student_id', 'subject_id', 'exam_type', 'marks_obtained', 'max_marks'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
