<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $table = 'submissions';
    const CREATED_AT = 'submitted_at';
    const UPDATED_AT = null;

    protected $fillable = ['assignment_id', 'student_id', 'file_path', 'status'];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
