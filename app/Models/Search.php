<?php
/**
 * @link https://www.twilio.com/blog/build-live-search-box-laravel-livewire-mysql
 */

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Builder;
use function Symfony\Component\String\u;

trait Search
{
    protected function scopeSearch(Builder $query, $term)
    {
        //$query->where('title', 'like', $term);
        $query->where('$text', ['$search' => $term]);
        $query->orderBy('published_at', 'DESC');

        return $query;
    }
}
