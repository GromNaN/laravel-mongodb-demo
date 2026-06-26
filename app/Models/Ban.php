<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Ban extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'bans';

    protected $fillable = [
        'username',
        'ip',
        'email',
        'message',
        'expiration',
        'ban_creator',
    ];

    protected $casts = [
        'expiration' => 'datetime',
    ];
}
