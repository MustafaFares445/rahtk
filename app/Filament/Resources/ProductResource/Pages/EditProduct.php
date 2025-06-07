<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use App\Models\Product;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProductResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Handle saving the record with media processing
     */
    protected function handleRecordUpdate($record, array $data): Product
    {
        // Update the main record
        $record->update($data);

        // Handle relationship data if type has changed
        if (isset($data['type'])) {
            ProductResource::handleRelationshipData($record, $data);
        }

        return $record->refresh();
    }

    /**
     * Delete a media item
     */
    public function deleteMedia(int $mediaId): void
    {
        try {
            $media = $this->record->media()->findOrFail($mediaId);
            $media->delete();

            Notification::make()
                ->title('Media deleted successfully')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error deleting media')
                ->body('The media could not be deleted. Please try again.')
                ->danger()
                ->send();
        }
    }

    /**
     * Mutate form data before filling the form
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Don't pre-fill the new media fields
        $data['new_images'] = null;
        $data['new_video'] = null;

        return $data;
    }

    /**
     * Mutate form data before saving
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle relationship data based on type
        if (isset($data['type'])) {
            $typeFields = $this->getTypeSpecificData($data);
            if (!empty($typeFields)) {
                $data['relationship_data'] = $typeFields;
            }
        }

        return $data;
    }

    /**
     * Extract type-specific fields from form data
     */
    private function getTypeSpecificData(array $data): array
    {
        $type = $data['type'];
        $typeData = [];

        // Define which fields belong to which type
        $fieldMappings = [
            'estate' => ['rooms', 'area', 'floors_number', 'is_furnished', 'floor'],
            'school' => ['quate', 'working_duration', 'founding_date', 'address', 'manager', 'manager_description'],
            'car' => ['model', 'year', 'kilo'],
            'electronic' => ['model', 'brand', 'year'],
            'farm' => ['type', 'bedrooms', 'bathrooms', 'floors_number', 'size'],
            'building' => ['type', 'brand', 'options'],
        ];

        if (isset($fieldMappings[$type])) {
            foreach ($fieldMappings[$type] as $field) {
                if (isset($data[$field])) {
                    $typeData[$field] = $data[$field];
                }
            }
        }

        return $typeData;
    }

    public function deleteAllMedia($collection)
    {
        if ($this->record) {
            $this->record->clearMediaCollection($collection);
            $this->record->refresh();
        }
    }
}
