<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $table = 'timetable';
    public $timestamps = false;

    protected $fillable = [
        'department_id', 'course_id', 'semester', 
        'subject_id', 'faculty_id', 'day_of_week', 
        'start_time', 'end_time', 'room'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }
}
