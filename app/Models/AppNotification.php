<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'notifications';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'title', 'message', 'type', 'icon',
        'is_read', 'link'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Send a notification to a user
     */
    public static function send(int $userId, string $title, string $message, string $type = 'info', ?string $link = null, string $icon = 'fas fa-bell'): self
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'is_read' => false,
            'link' => $link,
        ]);
    }
}
