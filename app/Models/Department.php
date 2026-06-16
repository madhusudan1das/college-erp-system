<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';
    public $timestamps = false;

    protected $fillable = ['name', 'code'];

    public function courses()
    {
        return $this->hasMany(Course::class, 'department_id');
    }

    public function faculty()
    {
        return $this->hasMany(Faculty::class, 'department_id');
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, Course::class, 'department_id', 'course_id', 'id', 'id');
    }
}
