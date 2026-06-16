<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    protected $table = 'hostels';
    public $timestamps = false;

    protected $fillable = ['name', 'type', 'capacity', 'address'];

    public function allotments()
    {
        return $this->hasMany(HostelAllotment::class, 'hostel_id');
    }
}
