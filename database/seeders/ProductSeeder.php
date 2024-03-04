<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = 'database/seeders/Products.sql';
        if(file_exists($path)) {
            DB::unprepared(file_get_contents($path));
            return;
        }

        $solarPanels = Product::factory()->count(12)->make([
            'productType' => 'solarPanel',
            'stock' => 1000,
            'initialStock' => 1000,
        ])->values();

        $inverters = Product::factory()->count(1)->make([
            'productType' => 'inverter',
            'stock' => 100,
            'initialStock' => 100,
        ])->values();

        $converters = Product::factory()->count(12)->make([
            'productType' => 'optimizer',
            'stock' => 500,
            'initialStock' => 500,
        ])->values();




        foreach($solarPanels as $product) {
            $product->save();
        }

        foreach($converters as $product) {
            $product->save();
        }

        foreach($inverters as $product) {
            $product->save();
        }
    }
}
