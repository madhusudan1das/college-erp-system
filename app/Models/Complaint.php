<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $table = 'complaints';
    public $timestamps = false;

    protected $fillable = ['user_id', 'title', 'description', 'status', 'resolution'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
