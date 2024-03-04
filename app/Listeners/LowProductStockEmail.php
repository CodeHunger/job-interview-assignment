<?php

namespace App\Listeners;

use App\Events\LowProductStockEvent;
use App\Mail\LowProductStock;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;

class LowProductStockEmail
{
    public function handle(LowProductStockEvent $event): void
    {
        $mail = new LowProductStock(Product::whereIn('id', $event->productIds)->get());
        Mail::to('info@2solar.nl')->send($mail);
    }
}
