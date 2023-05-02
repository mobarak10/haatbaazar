<?php

namespace Database\Factories;

use App\Models\LoanAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LoanAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->unique()->e164PhoneNumber,
            'address' => $this->faker->address,
            'deleted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
