<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
            AdSeeder::class,
            SchoolSeeder::class,
            SchoolClassSeeder::class,
            TeacherSeeder::class,
            ClassTeacherSeeder::class,
            // EstateSeeder::class,
            CarSeeder::class,
            ElectronicSeeder::class,
            FarmSeeder::class,
            BuildingSeeder::class,
        ]);
    }
}
