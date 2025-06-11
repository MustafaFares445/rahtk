<?php

namespace App\Filament\Resources\SchoolResource\Pages;

use App\Models\School;
use App\Models\Product;
use App\Enums\ProductTypes;
use App\Filament\Resources\SchoolResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSchool extends CreateRecord
{
    protected static string $resource = SchoolResource::class;

    protected function handleRecordCreation(array $data): School
    {
        // Create the School with the product_id
        return School::create([
            ...$data,
            'product_id' => 1,
        ]);
    }
}