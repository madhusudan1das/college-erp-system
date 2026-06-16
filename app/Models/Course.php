<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    public $timestamps = false;

    protected $fillable = ['name', 'department_id', 'duration_years'];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'course_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'course_id');
    }
}
