<?php

namespace Database\Seeders;

use App\Models\ClassTeacher;
use Illuminate\Database\Seeder;

class ClassTeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClassTeacher::query()->insert([
            [
                'teacher_id' => 1,
                'school_class_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 2,
                'school_class_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 3,
                'school_class_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 4,
                'school_class_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 5,
                'school_class_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 6,
                'school_class_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 1,
                'school_class_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 4,
                'school_class_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
