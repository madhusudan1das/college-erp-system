<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';
    public $timestamps = false;

    protected $fillable = ['name', 'code', 'course_id', 'semester', 'faculty_id'];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'subject_id');
    }

    public function marks()
    {
        return $this->hasMany(Mark::class, 'subject_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'subject_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'subject_id');
    }
}
