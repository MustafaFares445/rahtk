<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Ad;

class AdFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ad::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Ad $ad) {
            // Add a fake image to the ad
            $ad->addMediaFromUrl('https://picsum.photos/200/300')
                ->toMediaCollection('ads');
        });
    }
}
