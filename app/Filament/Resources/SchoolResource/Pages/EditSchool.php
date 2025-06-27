<?php

namespace App\Filament\Resources\SchoolResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SchoolResource;
use Filament\Forms\Form;

class EditSchool extends EditRecord
{
    protected static string $resource = SchoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $form = $this->form;
        $record = $this->record;

        $schoolTeachers = $form->getState()['school_teachers'] ?? [];
        $schoolClasses = $form->getState()['school_classes'] ?? [];

        // Map temp_key to teacher ID
        $tempKeyToId = [];
        foreach ($record->teachers as $teacher) {
            $tempKey = $teacher->temp_key; // Make sure temp_key is fillable and saved
            if ($tempKey) {
                $tempKeyToId[$tempKey] = $teacher->id;
            }
        }

        // Update each class with the correct teacher_id
        foreach ($record->schoolClasses as $class) {
            $classData = collect($schoolClasses)->firstWhere('id', $class->id);
            if ($classData && isset($classData['teacher_temp_key'])) {
                $teacherId = $tempKeyToId[$classData['teacher_temp_key']] ?? null;
                if ($teacherId) {
                    $class->teacher_id = $teacherId;
                    $class->save();
                }
            }
        }
    }
}
