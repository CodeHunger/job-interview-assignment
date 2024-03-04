<?php

namespace App\Http\Controllers;

use App\Models\Product;
use JsonSerializable;

readonly class ProductAmountViewModel implements JsonSerializable
{
    public function __construct(
        public int $id,
        public ?array $properties,
        public string $productType,
        public int $amount,
    ) {
    }

    public static function fromProduct(Product $product, int $amount = 10): ProductAmountViewModel
    {
        return new ProductAmountViewModel(
            $product->getAttribute('id'),
            $product->getAttribute('properties'),
            $product->getAttribute('productType'),
            $amount,
        );
    }

    public function jsonSerialize(): array
    {
        return (array) $this;
    }
}
