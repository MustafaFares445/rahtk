<?php

namespace Database\Factories;

use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\ClassTeacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassTeacherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClassTeacher::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'teacher_id' => Teacher::factory(),
            'class_id' => SchoolClass::factory(),
        ];
    }
}
