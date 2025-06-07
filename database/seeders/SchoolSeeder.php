<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run()
    {
        $schools = [
            [
                'product_id' => 3,
                'quate' => 'العلم نور',
                'working_duration' => 'من 7 صباحاً إلى 2 ظهراً',
                'founding_date' => '2010-05-15',
                'address' => 'حي المروج، جدة',
                'manager' => 'أ. علي محمد',
                'manager_description' => 'خبرة أكثر من 15 عاماً في مجال التعليم',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 9,
                'quate' => 'معاً نحو القمة',
                'working_duration' => 'من 7:30 صباحاً إلى 2:30 ظهراً',
                'founding_date' => '2015-08-20',
                'address' => 'حي الروضة، الرياض',
                'manager' => 'أ. محمد عبدالله',
                'manager_description' => 'خبرة 10 سنوات في إدارة المدارس الدولية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($schools as $school) {
            $schoolRecord = School::create($school);

            // Add a fake online image
            $schoolRecord->addMediaFromUrl('https://picsum.photos/200/300')
                         ->toMediaCollection('images');

            $schoolRecord->addMediaFromUrl('https://picsum.photos/200/300')
                         ->toMediaCollection('managers-images');

            $schoolRecord->addMediaFromUrl('https://picsum.photos/200/300')
                         ->toMediaCollection('services-images');
        }
    }
}
