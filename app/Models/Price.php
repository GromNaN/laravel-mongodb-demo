<?php

namespace App\Models;

use App\Models\Enum\Currency;
use MongoDB\Laravel\Eloquent\Model;

class Price extends Model
{
    /**
     * @var bool Disable timestamps for this model.
     */
    public $timestamps = false;

    protected $casts = [
        'amount' => 'float',
        'currency' => Currency::class
    ];
}
