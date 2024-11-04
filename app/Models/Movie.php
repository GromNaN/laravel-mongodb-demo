<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Movie extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'movies';

    public function comments()
    {
        return $this->hasMany(Comment::class, 'movie_id');
    }

    public function getIdAttribute($value = null)
    {
        return $this->attributes['id'] ?? $this->attributes['_id'] ?? null;
    }
}
