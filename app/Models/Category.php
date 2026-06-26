<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Category extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'categories';

    protected $fillable = [
        'name',
        'position',
    ];

    protected $casts = [
        'position' => 'int',
    ];

    public function forums()
    {
        return $this->hasMany(Forum::class, 'category_id');
    }
}
