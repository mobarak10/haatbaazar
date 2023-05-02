<?php

namespace Database\Factories;

use App\Models\Showroom;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
{
    protected $model = Stock::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'product_id' => null, // from seeder product_id
            'showroom_id' => DB::table('showrooms')->get()->random()->id, // for this must stop showroom global scope or manually insert showroom id
            'quantity' => $this->faker->randomDigit(),
            'damage_quantity' => 0,
            'average_purchase_price' => null, // get from seeder
        ];
    }
}
