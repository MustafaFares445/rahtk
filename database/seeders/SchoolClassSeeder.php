<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    public function run()
    {
        SchoolClass::query()->insert([
            [
                'name' => 'الصف الأول الابتدائي',
                'type' => 'initial',
                'school_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الصف الثاني المتوسط',
                'type' => 'principal',
                'school_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الصف الثالث الثانوي',
                'type' => 'secondary',
                'school_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'رياض الأطفال',
                'type' => 'initial',
                'school_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الصف الرابع الابتدائي',
                'type' => 'principal',
                'school_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الصف الأول الثانوي',
                'type' => 'secondary',
                'school_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
