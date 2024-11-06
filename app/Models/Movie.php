<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use MongoDB\Laravel\Eloquent\Model;

class Movie extends Model
{
    use Searchable;

    protected $connection = 'mongodb';
    protected $table = 'movies';

    // Always load comments
    // https://laravel.com/docs/11.x/eloquent-relationships#eager-loading-by-default
    protected $with = ['comments'];

    public function comments()
    {
        return $this->hasMany(Comment::class, 'movie_id');
    }

    // _id is kept as ObjectId in foreign key
    public function getIdAttribute($value = null)
    {
        return $this->attributes['id'] ?? $this->attributes['_id'] ?? null;
    }

    // The Search engine require ID to be a string
    // This is only required because we overloaded getIdAttribute
    public function getScoutKey(): string
    {
        return (string) $this->getKey();
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();

        // Customize the data array...

        // Set a default poster image, this is useful for the preview
        $array['poster'] ??= 'https://placehold.co/400x600?text='.urlencode($array['title']);

        // Hide sensitive data
        $array['comments'] = $this->comments->map(function ($comment) {
            return $comment->only(['name', 'text', 'date']);
        });

        return $array;
    }

}
