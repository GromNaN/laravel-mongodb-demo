<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Report extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'reports';

    protected $fillable = [
        'post_id',
        'topic_id',
        'forum_id',
        'reported_by',
        'created',
        'message',
        'zapped',
        'zapped_by',
    ];

    protected $casts = [
        'created' => 'datetime',
        'zapped'  => 'datetime',
    ];

    public function scopePending($query)
    {
        return $query->whereNull('zapped');
    }
}
