<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    protected $table = 'leave_applications';
    public $timestamps = false;

    protected $fillable = ['user_id', 'start_date', 'end_date', 'reason', 'status', 'comments', 'document_path'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
