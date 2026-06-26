<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Post extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'posts';

    protected $fillable = [
        'topic_id',
        'forum_id',
        'poster',
        'poster_id',
        'poster_ip',
        'posted',
        'message',
        'hide_smilies',
        'edited',
        'edited_by',
        'num',
    ];

    protected $casts = [
        'posted' => 'datetime',
        'edited' => 'datetime',
        'hide_smilies' => 'bool',
        'num' => 'int',
    ];

    protected $attributes = [
        'hide_smilies' => false,
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    public function forum()
    {
        return $this->belongsTo(Forum::class, 'forum_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'poster_id');
    }
}
