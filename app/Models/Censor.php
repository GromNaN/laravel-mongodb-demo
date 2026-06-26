<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Censor extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'censoring';

    protected $fillable = [
        'search_for',
        'replace_with',
    ];
}
