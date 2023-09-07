<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use MongoDB\BSON\ObjectId;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\EmbedsOne;
use MongoDB\Laravel\Relations\HasOne;

/**
 * @property ObjectId $_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $published_at
 * @property string $name
 * @property string $description
 * @property array $properties
 */
class OrderProduct extends Model
{
    public $timestamps = false;

    protected $casts = [
        'name' => 'string',
        'quantity' => 'int',
    ];

    /**
     * Link to the original product for reference
     * @todo doesn't work with embeds
     */
    public function product(): HasOne
    {
        return $this->hasOne(Price::class);
    }

    /**
     * Embed the price when the order is paid
     */
    public function price(): EmbedsOne
    {
        return $this->embedsOne(Price::class);
    }
}
