<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Comment extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'comments';

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movie_id');
    }

    public function getIdAttribute($value = null)
    {
        return $this->attributes['id'] ?? $this->attributes['_id'] ?? null;
    }
}
