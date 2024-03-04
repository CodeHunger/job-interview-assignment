<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
