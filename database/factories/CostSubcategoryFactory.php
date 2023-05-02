<?php

namespace Database\Factories;

use App\Models\CostSubcategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CostSubcategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CostSubcategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = ucfirst($this->faker->unique()->word());

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->text(),
        ];
    }
}
