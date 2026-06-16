<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultyAttendance extends Model
{
    protected $table = 'faculty_attendance';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = ['faculty_id', 'date', 'status'];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }
}
