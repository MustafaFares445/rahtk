<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Farm;
use App\Models\Product;

class FarmFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Farm::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'type' => fake()->randomElement(["sell","rent"]),
            'address' => fake()->word(),
            'bedrooms' => fake()->randomNumber(),
            'bathrooms' => fake()->numberBetween(-1000, 1000),
            'floors' => fake()->numberBetween(-1000, 1000),
            'size' => fake()->randomNumber(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Farm $farm) {
            // Add an image to the farm item
            $farm->addMediaFromUrl('https://picsum.photos/200/300')
                 ->toMediaCollection('images');
        });
    }
}
