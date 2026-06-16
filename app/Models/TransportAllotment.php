<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportAllotment extends Model
{
    protected $table = 'transport_allotments';
    public $timestamps = false;

    protected $fillable = ['transport_id', 'student_id'];

    public function transport()
    {
        return $this->belongsTo(Transport::class, 'transport_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
