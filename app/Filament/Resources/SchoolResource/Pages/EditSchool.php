<?php

namespace App\Filament\Resources\SchoolResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Log;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SchoolResource;

class EditSchool extends EditRecord
{
    protected static string $resource = SchoolResource::class;

    protected array $schoolClassesData = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load the product relationship data
        if ($this->record->product) {
            $data['product'] = [
                'title' => $this->record->product->title,
                'description' => $this->record->product->description,
                'address' => $this->record->product->address,
            ];
        }

        return $data;
    }
}
