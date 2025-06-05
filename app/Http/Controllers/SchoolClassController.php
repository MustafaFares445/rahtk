<?php

namespace App\Http\Controllers;

use App\Http\Resources\SchoolClassResource;
use App\Models\SchoolClass;

class SchoolClassController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/school/classes/{schoolClass}",
     *     summary="Get a school class by ID",
     *     tags={"School Classes"},
     *     @OA\Parameter(
     *         name="schoolClass",
     *         in="path",
     *         required=true,
     *         description="ID of the school class",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/SchoolClassResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="School class not found"
     *     )
     * )
     */
    public function __invoke(SchoolClass $schoolClass)
    {
        return SchoolClassResource::make(
            $schoolClass->load('media' , 'teachers.media')
        );
    }
}
