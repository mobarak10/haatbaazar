<?php

namespace Database\Factories;

use App\Models\LoanInstallment;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanInstallmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LoanInstallment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'loan_id' => null, // from seeder
            'date' => $this->faker->date(),
            'amount' => $this->faker->numberBetween(-1000, 1000),
            'adjustment' => 0,
            'note' => $this->faker->text,
            'deleted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
