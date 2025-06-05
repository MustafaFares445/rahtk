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
 *         property="quate",
 *         type="string",
 *         description="The quote of the school"
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
 *         property="servicesImages",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/MediaResource"),
 *         description="The list of service images"
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
            'quate' => $this->quate,
            'workingDuration' => $this->working_duration,
            'foundingDate' => $this->founding_date,
            'address' => $this->address,
            'manager' => $this->manager,
            'managerDescription' => $this->manager_description,
            'schoolClasses' => SchoolClassResource::collection($this->whenLoaded('schoolClasses')),
            'managerImage' => MediaResource::make($this->getFirstMedia('managers-images')),

            'servicesImages' => MediaResource::collection($this->whenLoaded('media' , $this->getMedia('services-images'))),
        ];
    }
}