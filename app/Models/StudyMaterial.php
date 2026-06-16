<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyMaterial extends Model
{
    protected $table = 'study_materials';
    public $timestamps = false;

    protected $fillable = ['title', 'description', 'file_path', 'subject_id', 'faculty_id'];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }
}
