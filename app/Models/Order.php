<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Product
 *
 * @mixin         Builder
 * @property      int $id
 * @property      int $user_id
 * @property      \Illuminate\Support\Carbon|null $created_at
 * @property      \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method        static \Database\Factories\OrderFactory factory($count = null, $state = [])
 * @method        static Builder|Order newModelQuery()
 * @method        static Builder|Order newQuery()
 * @method        static Builder|Order query()
 * @method        static Builder|Order whereCreatedAt($value)
 * @method        static Builder|Order whereId($value)
 * @method        static Builder|Order whereUpdatedAt($value)
 * @method        static Builder|Order whereUserId($value)
 * @mixin         Eloquent
 */
class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
    ];

    protected $hidden = [
        'stock',
        'properties',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
        )->withPivot('amount');
    }
}
