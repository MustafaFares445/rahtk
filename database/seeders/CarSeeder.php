<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Car::query()->insert([
            [
                'product_id' => 2,
                'model' => 'كامري',
                'year' => '2020-01-01',
                'kilo' => 45000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 7,
                'model' => 'أكسنت',
                'year' => '2019-01-01',
                'kilo' => 60000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 11,
                'model' => 'سوناتا',
                'year' => '2021-01-01',
                'kilo' => 25000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 12,
                'model' => 'كيا سيراتو',
                'year' => '2018-01-01',
                'kilo' => 70000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
