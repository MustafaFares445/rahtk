<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Contact::query()->create([
            'telegram' => '+1234567890',
            'whatsapp' => '+1234567890',
            'phone' => '+1234567890',
            'instagram' => 'https://instagram.com/example',
            'facebook' => 'https://facebook.com/example'
        ]);
    }
}
