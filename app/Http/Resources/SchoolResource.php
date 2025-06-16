<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="SchoolResource",
 *     type="object",
 *     title="School Resource",
 *     description="School resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The ID of the school"
 *     ),
 *     @OA\Property(
 *         property="workingDuration",
 *         type="string",
 *         description="The working duration of the school"
 *     ),
 *     @OA\Property(
 *         property="foundingDate",
 *         type="string",
 *         format="date",
 *         description="The founding date of the school"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string",
 *         description="The address of the school"
 *     ),
 *     @OA\Property(
 *         property="manager",
 *         type="string",
 *         description="The manager of the school"
 *     ),
 *     @OA\Property(
 *         property="managerDescription",
 *         type="string",
 *         description="The description of the manager"
 *     ),
 *     @OA\Property(
 *         property="schoolClasses",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/SchoolClassResource"),
 *         description="The list of school classes"
 *     ),
 *     @OA\Property(
 *         property="managerImage",
 *         ref="#/components/schemas/MediaResource",
 *         description="The image of the manager"
 *     ),
 *     @OA\Property(
 *         property="primaryImage",
 *         ref="#/components/schemas/MediaResource",
 *         description="The primary image of the school"
 *     ),
 *     @OA\Property(
 *         property="media",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/MediaResource"),
 *         description="The list of media associated with the school"
 *     )
 * )
 */
class SchoolResource extends JsonResource
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
            'workingDuration' => $this->working_duration,
            'foundingDate' => $this->founding_date,
            'address' => $this->address,
            'manager' => $this->manager,
            'managerDescription' => $this->manager_description,
            'schoolClasses' => SchoolClassResource::collection($this->whenLoaded('schoolClasses')),
            'managerImage' => MediaResource::make($this->getFirstMedia('managers-images')),
            'primaryImage' => MediaResource::make($this->getFirstMedia('primary-image')),
            'media' => $this->when($this->getAllMedia, MediaResource::collection(
                $this->media->filter(function ($media) {
                    return in_array($media->collection_name, ['primary-image']);
                })
            )),
        ];
    }
}