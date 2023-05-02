<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::all()->each(function (Product $product) {
            Stock::factory()
                ->count(1)
                ->create([
                    'product_id' => $product->id,
                    'average_purchase_price' => $product->purchase_price,
                    'divisor_number' => $product->divisor_number,
                ]);
        });
    }
}
