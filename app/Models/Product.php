<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use MongoDB\BSON\ObjectId;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;
use MongoDB\Laravel\Relations\BelongsToMany;
use MongoDB\Laravel\Relations\EmbedsMany;
use MongoDB\Laravel\Relations\EmbedsOne;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property ObjectId $_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $published_at
 * @property string $name
 * @property string $description
 * @property array $properties
 * @property Price[] $prices
 * @property Price $usdPrice
 */
class Product extends Model
{
    use HasSlug;
    use SoftDeletes;

    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        //'properties' => 'array', incorrect, would JSON serialize
        // @fixme cast on subproperties
        // @todo cast on objects?
        'properties.picture' => 'string',
        'properties.color' => 'string',
        'properties.size' => 'int',
    ];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }

    public function prices(): EmbedsMany
    {
        return $this->embedsMany(Price::class);
    }

    // @todo
    public function usdPrice(): EmbedsOne
    {
        return $this->prices()->one(function ($query) {
            $query->where('currency', 'USD');
        });
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
}
