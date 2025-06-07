<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $teachers = [
            [
                'name' => 'أ. محمد أحمد',
                'school_id' => 1,
                'job_title' => 'معلم لغة عربية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'أ. سعاد خالد',
                'school_id' => 1,
                'job_title' => 'معلمة رياضيات',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'أ. عبدالله علي',
                'school_id' => 1,
                'job_title' => 'معلم علوم',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'أ. منى سليمان',
                'school_id' => 2,
                'job_title' => 'معلمة لغة إنجليزية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'أ. خالد فهد',
                'school_id' => 2,
                'job_title' => 'معلم فيزياء',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'أ. نورة عبدالرحمن',
                'school_id' => 2,
                'job_title' => 'معلمة تربية فنية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($teachers as $teacherData) {
            $teacher = Teacher::query()->create($teacherData);

            // Add a fake online image to the 'profile_images' collection
            $teacher->addMediaFromUrl('https://picsum.photos/200/300') // Example URL for a random image
                    ->toMediaCollection('teachers-images');
        }
    }
}
