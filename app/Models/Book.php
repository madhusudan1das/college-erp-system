<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';
    public $timestamps = false;

    protected $fillable = ['title', 'author', 'isbn', 'category', 'quantity', 'available_quantity'];

    public function issues()
    {
        return $this->hasMany(BookIssue::class, 'book_id');
    }
}
