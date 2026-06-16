<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableUpload extends Model
{
    protected $table = 'timetable_uploads';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'file_path', 'original_filename', 'mime_type', 'uploaded_by',
        'status', 'slots_created', 'slots_skipped', 'conflicts_found',
        'unmatched_entries', 'ai_raw_response', 'ai_source',
        'processing_summary', 'error_message'
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
