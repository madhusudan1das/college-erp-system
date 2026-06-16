<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $table = 'faculty';
    public $timestamps = false;

    protected $fillable = ['user_id', 'first_name', 'last_name', 'department_id', 'phone', 'address'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'faculty_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'faculty_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'faculty_id');
    }

    public function attendanceMarked()
    {
        return $this->hasMany(Attendance::class, 'faculty_id');
    }

    public function attendance()
    {
        return $this->hasMany(FacultyAttendance::class, 'faculty_id');
    }
}
