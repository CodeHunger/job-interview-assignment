<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $path = 'database/seeders/Orders.sql';
        if(file_exists($path)) {
            DB::unprepared(file_get_contents($path));
            return;
        }

        $order = Order::factory()->makeOne([
            'user_id' => 1
        ]);


        $order->save();
        $order->products()->attach(1, ['amount' => 5]);
        $order->products()->attach(1001, ['amount' => 5]);
        $order->products()->attach(1501, ['amount' => 1]);
    }
}
