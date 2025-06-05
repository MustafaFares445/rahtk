<?php

namespace Database\Seeders;

use App\Models\Ad;
use Illuminate\Database\Seeder;

class AdSeeder extends Seeder
{
    public function run()
    {
        Ad::query()->insert([
            [
                'title' => 'عروض الصيف على العقارات',
                'start_date' => '2025-06-01 00:00:00',
                'end_date' => '2025-08-31 23:59:59',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'خصومات المدارس للعام الجديد',
                'start_date' => '2025-07-01 00:00:00',
                'end_date' => '2025-09-30 23:59:59',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'تخفيضات السيارات المستعملة',
                'start_date' => '2025-05-15 00:00:00',
                'end_date' => '2025-07-15 23:59:59',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'عروض الأجهزة الإلكترونية',
                'start_date' => '2025-06-10 00:00:00',
                'end_date' => '2025-07-10 23:59:59',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
