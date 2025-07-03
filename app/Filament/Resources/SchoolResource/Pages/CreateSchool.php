<?php

namespace App\Filament\Resources\SchoolResource\Pages;

use App\Models\School;
use App\Models\SchoolClass;
use App\Filament\Resources\SchoolResource;
use App\Models\Product;
use App\Models\Teacher;
use Filament\Resources\Pages\CreateRecord;

class CreateSchool extends CreateRecord
{
    protected static string $resource = SchoolResource::class;

    protected function handleRecordCreation(array $data): School
    {
        $product = Product::query()->create([
            'title' => $data['product']['title'],
            'description' =>$data['product']['description'],
            'view' => 0,
            'address' => $data['product']['address'],
            'type' => 'school'
        ]);

        $school = School::create([
            ...$data,
            'product_id' => $product->id
        ]);

        return $school;
    }


    protected function afterSave(): void
    {
        Teacher::query()
            ->where('school_id', 1)
            ->update(['school_id' => $this->record->id]);
    }
}
