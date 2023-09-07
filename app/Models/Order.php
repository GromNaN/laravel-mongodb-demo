<?php

namespace App\Models;

use App\Models\Enum\Currency;
use Illuminate\Support\Carbon;
use MongoDB\BSON\ObjectId;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;
use MongoDB\Laravel\Relations\BelongsTo;
use MongoDB\Laravel\Relations\EmbedsMany;
use MongoDB\Laravel\Relations\EmbedsOne;
use Spatie\Sluggable\SlugOptions;

/**
 * @property ObjectId $_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $paid_at
 * @property product[] $products
 */
class Order extends Model
{
    use SoftDeletes;

    protected $casts = [
        'paid_at' => 'immutable_datetime',
        'currency' => Currency::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function savePayment(): void
    {
        /** @var Product $product */
        foreach ($this->products as $product) {
            $product->save();
        }

        $this->paid_at = Carbon::now();
        $this->save();
    }

    public function products(): EmbedsMany
    {
        return $this->embedsMany(OrderProduct::class);
    }

    public function billing_address(): EmbedsOne
    {
        return $this->embedsOne(Address::class);
    }

    public function delivery_address(): EmbedsOne
    {
        return $this->embedsOne(Address::class);
    }

    // @todo
    public function usd_price(): EmbedsOne
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
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }
}
