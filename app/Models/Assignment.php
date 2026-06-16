<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $table = 'assignments';
    public $timestamps = false;

    protected $fillable = ['title', 'description', 'subject_id', 'faculty_id', 'file_path', 'deadline'];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'assignment_id');
    }
}
