<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class OnlineUser extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'online';

    protected $fillable = [
        'user_id',
        'ident',
        'logged',
        'idle',
        'last_post',
        'last_search',
    ];

    protected $casts = [
        'user_id'     => 'int',
        'idle'        => 'bool',
        'logged'      => 'datetime',
        'last_post'   => 'datetime',
        'last_search' => 'datetime',
    ];

    protected $attributes = [
        'idle' => false,
    ];
}
