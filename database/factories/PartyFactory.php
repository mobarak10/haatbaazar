<?php

namespace Database\Factories;

use App\Models\Party;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class PartyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Party::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = ucfirst($this->faker->word());

        return [
            'name' => $name,
            'genus' => $this->faker->randomElement(['customer', 'supplier']),
            'description' => $this->faker->text(),
            'phone' => $this->faker->phoneNumber,
            'balance' => 0.00,
            'showroom_id' => DB::table('showrooms')->get()->random()->id, // for this must stop showroom global scope or manually insert showroom id
            'active' => true,
        ];
    }
}
