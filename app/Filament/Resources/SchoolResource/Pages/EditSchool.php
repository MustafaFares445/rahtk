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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Get the raw form state to access all nested data including tabs
        $rawFormState = $this->form->getRawState();

        dd($rawFormState);

        // Try to extract school_classes from different possible locations
        $schoolClasses = [];


        // Check direct access first
        if (isset($data['school_classes'])) {
            $schoolClasses = $data['school_classes'];
            Log::info('Found school_classes in processed data');
        }


        // Check in raw form state
        elseif (isset($rawFormState['school_classes'])) {
            $schoolClasses = $rawFormState['school_classes'];
            Log::info('Found school_classes in raw form state');
        }
        // Check if it's nested in tabs or other structure
        else {
            // Search recursively in the raw form state
            $schoolClasses = $this->findSchoolClassesInFormData($rawFormState);
            if (!empty($schoolClasses)) {
                Log::info('Found school_classes in nested structure');
            }
        }

        // Store the school classes data
        $this->schoolClassesData = $schoolClasses;

        Log::info('Extracted school classes data:', [
            'count' => count($this->schoolClassesData),
            'data' => $this->schoolClassesData
        ]);

        return $data;
    }

    /**
     * Recursively search for school_classes data in nested form structure
     */
    private function findSchoolClassesInFormData(array $data): array
    {
        foreach ($data as $key => $value) {
            if ($key === 'school_classes' && is_array($value)) {
                return $value;
            }
            if (is_array($value)) {
                $result = $this->findSchoolClassesInFormData($value);
                if (!empty($result)) {
                    return $result;
                }
            }
        }
        return [];
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        Log::info('After save - Starting teacher-class linking:', [
            'school_id' => $record->id,
            'classes_data_count' => count($this->schoolClassesData)
        ]);

        // If we don't have school classes data, try to get it from the form one more time
        if (empty($this->schoolClassesData)) {
            $rawFormState = method_exists($this->form, 'getRawState') ? $this->form->getRawState() : [];
            $this->schoolClassesData = $this->findSchoolClassesInFormData($rawFormState);

            Log::info('Attempted to retrieve school classes data again:', [
                'found_count' => count($this->schoolClassesData)
            ]);
        }

        // If still no data, log and return
        if (empty($this->schoolClassesData)) {
            Log::warning('No school classes data found - skipping teacher-class linking');
            return;
        }

        // Refresh the record to get the latest data including relationships
        $record->refresh();

        // Create mapping from temp_key to actual teacher ID for newly created teachers
        $tempKeyToId = [];
        foreach ($record->teachers as $teacher) {
            if ($teacher->temp_key) {
                $tempKeyToId['temp_' . $teacher->temp_key] = $teacher->id;
            }
        }

        Log::info('Teacher mapping created:', [
            'temp_key_mappings' => $tempKeyToId,
            'total_teachers' => $record->teachers->count()
        ]);

        // Process each school class and link teachers
        dd($this->schoolClassesData);
        foreach ($this->schoolClassesData as $index => $classData) {
            Log::info('Processing class:', [
                'index' => $index,
                'class_name' => $classData['name'] ?? 'unknown',
                'has_id' => isset($classData['id']),
                'selected_teachers' => $classData['teacher'] ?? []
            ]);

            $schoolClass = null;

            // Try to find the school class
            if (isset($classData['id']) && is_numeric($classData['id'])) {
                // Existing class
                $schoolClass = $record->schoolClasses()->find($classData['id']);
                Log::info('Found existing class by ID:', ['id' => $classData['id'], 'found' => !!$schoolClass]);
            }

            // If not found by ID, try to find by name and type
            if (!$schoolClass && isset($classData['name'])) {
                $query = $record->schoolClasses()->where('name', $classData['name']);

                if (isset($classData['type'])) {
                    $query->where('type', $classData['type']);
                }

                $schoolClass = $query->latest()->first();
                Log::info('Found class by name/type:', [
                    'name' => $classData['name'],
                    'type' => $classData['type'] ?? 'not set',
                    'found' => !!$schoolClass
                ]);
            }

            if (!$schoolClass) {
                Log::warning('Could not find school class for data:', $classData);
                continue;
            }

            // Get the selected teachers from the form
            $selectedTeachers = $classData['teacher'] ?? [];

            if (empty($selectedTeachers)) {
                // No teachers selected, detach all
                $schoolClass->teachers()->detach();
                Log::info('Detached all teachers from class:', ['class_id' => $schoolClass->id]);
                continue;
            }

            $teacherIds = [];

            foreach ($selectedTeachers as $teacherIdentifier) {
                if (is_numeric($teacherIdentifier)) {
                    // This is an existing teacher ID
                    $teacherIds[] = (int)$teacherIdentifier;
                    Log::info('Added existing teacher:', ['id' => $teacherIdentifier]);
                } elseif (str_starts_with($teacherIdentifier, 'temp_')) {
                    // This is a temporary teacher, get the actual ID
                    if (isset($tempKeyToId[$teacherIdentifier])) {
                        $teacherIds[] = $tempKeyToId[$teacherIdentifier];
                        Log::info('Added temp teacher:', [
                            'temp_key' => $teacherIdentifier,
                            'actual_id' => $tempKeyToId[$teacherIdentifier]
                        ]);
                    } else {
                        Log::warning('Temp teacher not found in mapping:', [
                            'temp_key' => $teacherIdentifier,
                            'available_mappings' => array_keys($tempKeyToId)
                        ]);
                    }
                }
            }

            // Sync the teachers with the class
            if (!empty($teacherIds)) {
                $schoolClass->teachers()->sync($teacherIds);
                Log::info('Successfully synced teachers with class:', [
                    'class_id' => $schoolClass->id,
                    'class_name' => $schoolClass->name,
                    'teacher_ids' => $teacherIds,
                    'synced_count' => count($teacherIds)
                ]);
            } else {
                $schoolClass->teachers()->detach();
                Log::info('No valid teachers found, detached all from class:', [
                    'class_id' => $schoolClass->id,
                    'class_name' => $schoolClass->name
                ]);
            }
        }

        Log::info('Teacher-class linking completed');
    }
}