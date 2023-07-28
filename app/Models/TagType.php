<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class TagType extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function categories()
    {
        return $this->embedsMany(TagTypeCategory::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
