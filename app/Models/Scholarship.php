<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $table = 'scholarships';
    public $timestamps = false;

    protected $fillable = ['name', 'description', 'amount', 'student_id', 'status'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
