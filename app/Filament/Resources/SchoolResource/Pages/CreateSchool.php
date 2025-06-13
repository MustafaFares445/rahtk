<?php

namespace App\Filament\Resources\SchoolResource\Pages;

use App\Models\School;
use App\Filament\Resources\SchoolResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSchool extends CreateRecord
{
    protected static string $resource = SchoolResource::class;

    protected function handleRecordCreation(array $data): School
    {
        // Create the School with the product_id
        $school = School::create([
            ...$data,
            'product_id' => 1,
        ]);

        // Process teacher-class relationships after creation
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
}