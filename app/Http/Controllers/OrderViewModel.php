<?php

namespace App\Http\Controllers;

use App\Models\Order;
use JsonSerializable;

class OrderViewModel implements JsonSerializable {

    public array $products;
    public function __construct(
        public readonly int $id,
        public readonly string $created,
        iterable $products = [],
    )
    {
        $this->products = [];
        foreach ($products as $product) {
            $this->products[] = ProductAmountViewModel::fromProduct($product, $product->pivot->amount);
        }
    }

    public function jsonSerialize(): mixed {
        return (array) $this;
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
