<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $table = 'fees';
    public $timestamps = false;

    protected $fillable = ['student_id', 'title', 'amount', 'status', 'paid_at', 'transaction_no', 'razorpay_payment_id', 'payment_method'];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
