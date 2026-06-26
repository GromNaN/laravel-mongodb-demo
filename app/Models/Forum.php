<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Forum extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'forums';

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'position',
        'num_topics',
        'num_posts',
        'redirect_url',
        'sort_by',
        'moderators',
        'last_post',
        'permissions',
    ];

    protected $casts = [
        'position'    => 'int',
        'num_topics'  => 'int',
        'num_posts'   => 'int',
        'sort_by'     => 'int',
        'moderators'  => 'array',
        'last_post'   => 'array',
        'permissions' => 'array',
    ];

    protected $attributes = [
        'num_topics' => 0,
        'num_posts'  => 0,
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function topics()
    {
        return $this->hasMany(Topic::class, 'forum_id');
    }
}
