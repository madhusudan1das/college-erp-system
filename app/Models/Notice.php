<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $table = 'notices';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = ['title', 'content', 'attachment', 'role_id', 'created_by'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
