<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $connection = 'mongodb';

    protected $collection = 'users';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'username',
        'password',
        'email',
        'group_id',
        'title',
        'realname',
        'url',
        'location',
        'signature',
        'num_posts',
        'last_post',
        'last_visit',
        'last_active',
        'registered',
        'registration_ip',
        'avatar',
        'admin_note',
        'topic_subscriptions',
        'forum_subscriptions',
        'preferences',
        'email_setting',
        'is_banned',
    ];

    protected $casts = [
        'group_id'            => 'int',
        'num_posts'           => 'int',
        'email_setting'       => 'int',
        'is_banned'           => 'bool',
        'last_post'           => 'datetime',
        'last_visit'          => 'datetime',
        'last_active'         => 'datetime',
        'registered'          => 'datetime',
        'topic_subscriptions' => 'array',
        'forum_subscriptions' => 'array',
        'preferences'         => 'array',
    ];

    protected $attributes = [
        'group_id'  => Group::MEMBER,
        'num_posts'  => 0,
        'is_banned'  => false,
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'poster_id');
    }

    public function topics()
    {
        return $this->hasMany(Topic::class, 'poster_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_banned', false);
    }

    public function scopeByGroup($query, int $groupId)
    {
        return $query->where('group_id', $groupId);
    }
}
