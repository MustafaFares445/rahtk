<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="SchoolClassResource",
 *     type="object",
 *     title="School Class Resource",
 *     description="School Class Resource",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The ID of the school class"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the school class"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="The type of the school class"
 *     ),
 *     @OA\Property(
 *         property="teachers",
 *         type="array",
 *         description="List of teachers in the school class",
 *         @OA\Items(ref="#/components/schemas/TeacherResource")
 *     ),
 *     @OA\Property(
 *         property="media",
 *         type="array",
 *         description="List of media associated with the school class",
 *         @OA\Items(ref="#/components/schemas/MediaResource")
 *     )
 * )
 */
class SchoolClassResource extends JsonResource
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
            'type' => $this->type,
            'teachers' => TeacherResource::collection($this->whenLoaded('teachers')),
            'media' => $this->relationLoaded('media') ? MediaResource::collection($this->media) : [],
        ];
    }
}