<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Activity::create([
            'url' => 'https://example.com/activity1',
            'title' => 'Activity 1',
        ]);

        Activity::create([
            'url' => 'https://example.com/activity2',
            'title' => 'Activity 2',
        ]);

        // Add more activities as needed
    }
}
