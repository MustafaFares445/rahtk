<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    public function run()
    {
        Building::query()->insert([
            [
                'product_id' => 17,
                'type' => 'فيلا',
                'brand' => 'الراجحي للإنشاءات',
                'options' => 'حديقة، موقف سيارات، نظام أمني',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 18,
                'type' => 'عمارة سكنية',
                'brand' => 'بن لادن',
                'options' => '4 شقق، مصعد، مواقف سيارات',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 19,
                'type' => 'مجمع تجاري',
                'brand' => 'السدحان العقارية',
                'options' => '10 محلات، مواقف سيارات، نظام أمني',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}