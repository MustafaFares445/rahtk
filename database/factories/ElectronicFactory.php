<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Electronic;
use App\Models\Product;

class ElectronicFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Electronic::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'model' => fake()->word(),
            'brand' => fake()->company(),
            'year' => fake()->year(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Electronic $electronic) {
            // Add an image to the electronic item
            $electronic->addMediaFromUrl('https://picsum.photos/200/300')
                       ->toMediaCollection('images');
        });
    }
}
