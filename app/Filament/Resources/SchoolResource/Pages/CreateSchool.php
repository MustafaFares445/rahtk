<?php

namespace App\Filament\Resources\SchoolResource\Pages;

use App\Models\School;
use App\Models\SchoolClass;
use App\Filament\Resources\SchoolResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSchool extends CreateRecord
{
    protected static string $resource = SchoolResource::class;

    protected function handleRecordCreation(array $data): School
    {
        $school = School::create([
            ...$data,
            'product_id' => 1,
        ]);

        $this->processTeacherClassRelationships($school, $data);

        return $school;
    }

    protected function processTeacherClassRelationships(School $school, array $data): void
    {
        if (!isset($data['school_classes'])) {
            return;
        }

        // Create a mapping of teacher names to IDs for quick lookup
        $teacherMapping = [];
        if (isset($data['school_teachers'])) {
            foreach ($data['school_teachers'] as $index => $teacherData) {
                if (!empty($teacherData['name'])) {
                    $teacher = $school->teachers()
                        ->where('name', $teacherData['name'])
                        ->where('job_title', $teacherData['job_title'])
                        ->first();
                    if ($teacher) {
                        $teacherMapping["temp_$index"] = $teacher->id;
                    }
                }
            }
        }

        // Process each class and its teacher assignments
        foreach ($data['school_classes'] as $classIndex => $classData) {
            if (!isset($classData['teachers']) || !isset($classData['name'])) {
                continue;
            }

            $schoolClass = $school->schoolClasses()
                ->where('name', $classData['name'])
                ->first();

            if (!$schoolClass) {
                continue;
            }

            $teacherIds = [];
            foreach ($classData['teachers'] as $teacherIdentifier) {
                if (isset($teacherMapping[$teacherIdentifier])) {
                    $teacherIds[] = $teacherMapping[$teacherIdentifier];
                } elseif (is_numeric($teacherIdentifier)) {
                    $teacherIds[] = $teacherIdentifier;
                }
            }

            // Attach teachers to the class
            if (!empty($teacherIds)) {
                $schoolClass->teachers()->sync($teacherIds);
            }
        }
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

        // Process each class and assign teachers
        foreach ($record->schoolClasses as $class) {
            $classData = collect($schoolClasses)->firstWhere('id', $class->id);
            if ($classData && isset($classData['teacher_temp_key'])) {
                $teacherId = $tempKeyToId[$classData['teacher_temp_key']] ?? null;
                if ($teacherId) {
                    /** @var SchoolClass $class */
                    $class->teachers()->detach();
                    $class->teachers()->attach($teacherId);
                    $class->save();
                }
            } elseif ($classData && isset($classData['teachers'])) {
                // Handle existing teachers
                $teacherIds = [];
                foreach ($classData['teachers'] as $teacherIdentifier) {
                    if (is_numeric($teacherIdentifier)) {
                        $teacherIds[] = $teacherIdentifier;
                    }
                }
                if (!empty($teacherIds)) {
                    $class->teachers()->sync($teacherIds);
                }
            }
        }
    }
}