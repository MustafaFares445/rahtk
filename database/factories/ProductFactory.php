<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Product;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'slug' => fake()->slug(),
            'description' => fake()->text(),
            'price' => fake()->numberBetween(-100000, 100000),
            'is_urgent' => fake()->numberBetween(-100000, 100000),
            'discount' => fake()->numberBetween(-100000, 100000),
            'view' => fake()->randomNumber(),
        ];
    }
}
