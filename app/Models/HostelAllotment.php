<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelAllotment extends Model
{
    protected $table = 'hostel_allotments';
    public $timestamps = false;

    protected $fillable = ['hostel_id', 'room_no', 'student_id'];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
