<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Product
 *
 * @mixin         Builder
 * @property      int $id
 * @property      string $name
 * @property      string $productType
 * @property      int $stock
 * @property      int $initialStock
 * @property      string|null $properties
 * @property      \Illuminate\Support\Carbon|null $created_at
 * @property      \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $products
 * @property-read int|null $products_count
 * @method        static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method        static Builder|Product newModelQuery()
 * @method        static Builder|Product newQuery()
 * @method        static Builder|Product query()
 * @method        static Builder|Product whereCreatedAt($value)
 * @method        static Builder|Product whereId($value)
 * @method        static Builder|Product whereInitialStock($value)
 * @method        static Builder|Product whereName($value)
 * @method        static Builder|Product whereProductType($value)
 * @method        static Builder|Product whereProperties($value)
 * @method        static Builder|Product whereStock($value)
 * @method        static Builder|Product whereUpdatedAt($value)
 * @mixin         Eloquent
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'productType',
        'stock',
        'properties',
    ];

    protected $hidden = [
        'stock',
        'properties',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Order::class,
        )->withPivot('amount');
    }
}
