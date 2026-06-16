<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $table = 'salaries';
    public $timestamps = false;

    protected $fillable = ['faculty_id', 'base_salary', 'bonuses', 'deductions', 'pay_date', 'status'];

    protected $casts = [
        'pay_date' => 'date',
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }
}
