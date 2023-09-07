<?php

namespace App\Models;

use App\Models\Enum\Currency;
use MongoDB\Laravel\Eloquent\Model;

class Address extends Model
{
    protected $casts = [
        'street' => 'string',
        'city' => 'string',
        'state' => 'string',
        'zip' => 'string',
        'country' => 'string',
    ];
}
