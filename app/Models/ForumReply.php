<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model
{
    protected $table = 'forum_replies';
    public $timestamps = false;

    protected $fillable = ['topic_id', 'reply_text', 'user_id'];

    public function topic()
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
