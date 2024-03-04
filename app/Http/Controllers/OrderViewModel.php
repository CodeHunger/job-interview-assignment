<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use JsonSerializable;

readonly class OrderViewModel implements JsonSerializable
{
    public array $products;

    /**
     * @param int $id
     * @param string $created
     * @param Product[] $products
     */
    public function __construct(
        public int    $id,
        public string $created,
        iterable      $products = [],
    )
    {
        $productAmountViewModels = [];
        foreach ($products as $product) {
            $productAmountViewModels[] = ProductAmountViewModel::fromProduct($product, $product->pivot->amount);
        }

        $this->products = $productAmountViewModels;
    }

    public function jsonSerialize(): array
    {
        return (array)$this;
    }

    public static function fromOrder(Order $order): OrderViewModel
    {
        return new OrderViewModel(
            $order->getAttribute('id'),
            $order->getAttribute('created_at'),
            $order->products()->withPivot('amount')->get()
        );
    }
}
