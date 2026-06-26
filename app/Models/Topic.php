<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Topic extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'topics';

    protected $fillable = [
        'forum_id',
        'subject',
        'poster',
        'poster_id',
        'posted',
        'first_post_id',
        'last_post',
        'num_views',
        'num_replies',
        'closed',
        'sticky',
        'moved_to',
    ];

    protected $casts = [
        'posted'      => 'datetime',
        'num_views'   => 'int',
        'num_replies' => 'int',
        'closed'      => 'bool',
        'sticky'      => 'bool',
        'last_post'   => 'array',
    ];

    protected $attributes = [
        'num_views'   => 0,
        'num_replies' => 0,
        'closed'      => false,
        'sticky'      => false,
    ];

    public function forum()
    {
        return $this->belongsTo(Forum::class, 'forum_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'topic_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'poster_id');
    }
}
