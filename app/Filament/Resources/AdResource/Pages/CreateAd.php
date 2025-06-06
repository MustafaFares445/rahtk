<?php

namespace App\Filament\Resources\AdResource\Pages;

use App\Filament\Resources\AdResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAd extends CreateRecord
{
    protected static string $resource = AdResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove media from data as it's handled by SpatieMediaLibraryFileUpload
        unset($data['media']);
        return $data;
    }
}