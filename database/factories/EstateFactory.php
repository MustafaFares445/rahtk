<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Estate;
use App\Models\Product;

class EstateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Estate::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'rooms' => $this->faker->numberBetween(1, 5),
            'area' => $this->faker->numberBetween(50, 500),
            'floors_number' => $this->faker->numberBetween(1, 3),
            'is_furnished' => $this->faker->boolean,
            'address' => $this->faker->address,
            'floor' => $this->faker->numberBetween(1, 10),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Estate $estate) {
            // Add an image to the estate item
            $estate->addMediaFromUrl('https://picsum.photos/200/300')
                   ->toMediaCollection('images');
        });
    }
}
