<?php

namespace Database\Seeders;

use App\Models\Ad;
use Illuminate\Database\Seeder;

class AdSeeder extends Seeder
{
    public function run()
    {
        $ads = [
            [
                'title' => 'عروض الصيف على العقارات',
                'start_date' => now(),
                'end_date' => now()->addMonth(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'خصومات المدارس للعام الجديد',
                'start_date' => now(),
                'end_date' => now()->addMonth(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'تخفيضات السيارات المستعملة',
               'start_date' => now(),
                'end_date' => now()->addMonth(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'عروض الأجهزة الإلكترونية',
                'start_date' => now(),
                'end_date' => now()->addMonth(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($ads as $adData) {
            $ad = Ad::create($adData);

            // Add a fake online image using Spatie Media Library
            $ad->addMediaFromUrl('https://picsum.photos/800/600')
                ->toMediaCollection('images');
        }
    }
}
