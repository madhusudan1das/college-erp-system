<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiAuditLog extends Model
{
    protected $table = 'ai_audit_logs';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'action_type', 'user_id', 'target_table', 'target_id',
        'details', 'status', 'error_message', 'ip_address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
