<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'enrollment_no', 
        'course_id', 'current_semester', 'phone', 'address', 
        'dob', 'profile_image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function marks()
    {
        return $this->hasMany(Mark::class, 'student_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'student_id');
    }

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class, 'student_id');
    }
}
