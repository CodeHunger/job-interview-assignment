<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
