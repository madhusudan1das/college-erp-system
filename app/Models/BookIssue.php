<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookIssue extends Model
{
    protected $table = 'book_issues';
    public $timestamps = false;

    protected $fillable = ['book_id', 'user_id', 'issued_at', 'return_due_date', 'returned_at', 'fine_amount'];

    protected $casts = [
        'issued_at' => 'datetime',
        'return_due_date' => 'date',
        'returned_at' => 'datetime',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
