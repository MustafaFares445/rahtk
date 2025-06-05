<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\School;

class SchoolFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = School::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => fake()->name(),
            'principal' => fake()->word(),
            'working_duration' => fake()->word(),
            'founding_date' => fake()->numberBetween(-100000, 100000),
            'address' => fake()->word(),
            'manager' => fake()->word(),
            'manager_description' => fake()->text(),
        ];
    }
}
