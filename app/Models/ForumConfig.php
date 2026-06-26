<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ForumConfig extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'config';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Retrieve a config value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $record = static::where('key', $key)->first();

        return $record ? $record->value : $default;
    }

    /**
     * Set a config value by key, creating or updating as needed.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }

    /**
     * Return all config entries as an associative array keyed by config key.
     *
     * @return array<string, string>
     */
    public static function getAll(): array
    {
        return static::all()->pluck('value', 'key')->all();
    }
}
