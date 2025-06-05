<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="TeacherResource",
 *     type="object",
 *     title="Teacher Resource",
 *     description="Teacher resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The ID of the teacher"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the teacher"
 *     ),
 *     @OA\Property(
 *         property="school_id",
 *         type="integer",
 *         description="The ID of the school the teacher belongs to"
 *     ),
 *     @OA\Property(
 *         property="jobTitle",
 *         type="string",
 *         description="The job title of the teacher"
 *     ),
 *     @OA\Property(
 *         property="image",
 *         type="object",
 *         description="The image of the teacher",
 *         ref="#/components/schemas/MediaResource"
 *     )
 * )
 */
class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'school_id' => $this->school_id,
            'jobTitle' => $this->job_title,
            'image' => MediaResource::make($this->getFirstMedia('images')),
        ];
    }
}