<?php

namespace Database\Seeders;

use App\Models\Electronic;
use Illuminate\Database\Seeder;

class ElectronicSeeder extends Seeder
{
    public function run()
    {
        Electronic::query()->insert([
            [
                'product_id' => 4,
                'model' => 'آيفون 13 برو ماكس',
                'brand' => 'آبل',
                'year' => '2021',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 8,
                'model' => 'جالاكسي S22',
                'brand' => 'سامسونج',
                'year' => '2022',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 13,
                'model' => 'ماك بوك برو',
                'brand' => 'آبل',
                'year' => '2023',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 14,
                'model' => 'آيباد برو',
                'brand' => 'آبل',
                'year' => '2022',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}