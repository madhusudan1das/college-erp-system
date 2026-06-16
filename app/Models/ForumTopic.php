<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumTopic extends Model
{
    protected $table = 'forum_topics';
    public $timestamps = false;

    protected $fillable = ['title', 'content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'topic_id');
    }
}
