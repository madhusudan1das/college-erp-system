<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    protected $table = 'transports';
    public $timestamps = false;

    protected $fillable = ['route_name', 'vehicle_no', 'driver_name', 'phone'];

    public function allotments()
    {
        return $this->hasMany(TransportAllotment::class, 'transport_id');
    }
}
