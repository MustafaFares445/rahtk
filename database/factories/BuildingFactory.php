<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Building;
use App\Models\Product;

class BuildingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Building::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'type' => $this->faker->word,
            'brand' => $this->faker->company,
            'options' => json_encode(['color' => $this->faker->colorName, 'size' => $this->faker->randomElement(['small', 'medium', 'large'])]),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Building $building) {
            // Add a random image to the building
            $building->addMediaFromUrl($this->faker->imageUrl(640, 480, 'building'))
                    ->toMediaCollection('buildings');
        });
    }
}
