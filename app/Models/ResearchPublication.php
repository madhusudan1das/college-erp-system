<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchPublication extends Model
{
    protected $table = 'research_publications';
    public $timestamps = false;

    protected $fillable = ['faculty_id', 'title', 'journal_name', 'publication_date', 'description', 'file_path'];

    protected $casts = [
        'publication_date' => 'date',
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }
}
