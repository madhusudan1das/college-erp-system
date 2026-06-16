<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentoringRecord extends Model
{
    protected $table = 'mentoring_records';
    public $timestamps = false;

    protected $fillable = ['faculty_id', 'student_id', 'meeting_date', 'notes'];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
