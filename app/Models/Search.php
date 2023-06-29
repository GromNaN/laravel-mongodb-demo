<?php
/**
 * @link https://www.twilio.com/blog/build-live-search-box-laravel-livewire-mysql
 */

namespace App\Models;

use function Symfony\Component\String\u;

trait Search
{
    private function extractSearchTerms(string $searchQuery): array
    {
        $searchQuery = u($searchQuery)->replaceMatches('/[[:space:]]+/', ' ')->trim();
        $terms = array_unique($searchQuery->split(' '));

        // ignore the search terms that are too short
        return array_filter($terms, static function ($term) {
            return 2 <= $term->length();
        });
    }

    protected function scopeSearch($query, $term)
    {
        $searchTerms = $this->extractSearchTerms($term);

        foreach ($searchTerms as $term) {
            $query->orWhere($this->searchable, 'LIKE', "%{$term}%");
        }
        $query->orderBy('published_at', 'DESC');

        return $query;
    }
}
