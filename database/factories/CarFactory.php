<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Car;
use App\Models\Product;
use Illuminate\Http\UploadedFile;

class CarFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Car::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'model' => fake()->word(),
            'year' => fake()->year(),
            'kilo' => fake()->numberBetween(1000, 100000),
        ];
    }

    public function withImages()
    {
        return $this->afterCreating(function (Car $car) {
            $car->addMedia($this->faker->imageUrl(640, 480, 'car'))
                ->toMediaCollection('car_images');
        });
    }
}
