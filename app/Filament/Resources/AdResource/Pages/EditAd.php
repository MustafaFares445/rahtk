<?php

namespace App\Filament\Resources\AdResource\Pages;

use Filament\Actions;
use App\Filament\Resources\AdResource;
use Filament\Resources\Pages\EditRecord;

class EditAd extends EditRecord
{
    protected static string $resource = AdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}