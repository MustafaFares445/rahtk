<?php

namespace Database\Seeders;

use App\Models\Farm;
use Illuminate\Database\Seeder;

class FarmSeeder extends Seeder
{
    public function run()
    {
       Farm::query()->insert([
            [
                'product_id' => 5,
                'type' => 'sell',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'floors_number' => 1,
                'size' => 5000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 15,
                'type' => 'rent',
                'bedrooms' => 2,
                'bathrooms' => 1,
                'floors_number' => 1,
                'size' => 3000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 16,
                'type' => 'sell',
                'bedrooms' => 4,
                'bathrooms' => 3,
                'floors_number' => 2,
                'size' => 8000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}