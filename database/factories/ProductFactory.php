<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'unit_id' => Unit::all()->random()->id,
            'category_id' => Category::all()->random()->id,
            'brand_id' => Brand::all()->random()->id,
            'barcode' => $this->faker->ean13(),
            'sale_price' => $this->faker->numberBetween(1, 2000),
            'purchase_price' => $this->faker->numberBetween(1, 2000),
            'price_type' => 'Pcs',
            'stock_alert' => 10,
            'divisor_number' => 1,
            'quantity_in_unit' => ['10'],
            'type' => 'finish_product',
            'status' => 1,
        ];
    }
}
