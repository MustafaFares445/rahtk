<?php

namespace Database\Seeders;

use App\Models\Estate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
       Estate::query()->insert([
            [
                'product_id' => 1,
                'rooms' => 3,
                'area' => 180.00,
                'floors_number' => 1,
                'is_furnished' => 1,
                'floor' => 'الثالث',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 6,
                'rooms' => 5,
                'area' => 350.00,
                'floors_number' => 2,
                'is_furnished' => 1,
                'floor' => 'الأول',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 10,
                'rooms' => 2,
                'area' => 120.00,
                'floors_number' => 1,
                'is_furnished' => 0,
                'floor' => 'الرابع',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
