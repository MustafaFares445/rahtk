<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProductResource;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterSave(): void
    {
        parent::afterSave();

        $data = $this->form->getState();
        $record = $this->record;

        ProductResource::handleRelationshipData($record, $data);
    }
}
