<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="FarmResource",
 *     type="object",
 *     title="Farm Resource",
 *     description="Farm resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The ID of the farm"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="The type of the farm"
 *     ),
 *     @OA\Property(
 *         property="bedrooms",
 *         type="integer",
 *         description="The number of bedrooms in the farm"
 *     ),
 *     @OA\Property(
 *         property="bathrooms",
 *         type="integer",
 *         description="The number of bathrooms in the farm"
 *     ),
 *     @OA\Property(
 *         property="floorsNumber",
 *         type="integer",
 *         description="The number of floors in the farm"
 *     ),
 *     @OA\Property(
 *         property="size",
 *         type="integer",
 *         description="The size of the farm"
 *     )
 * )
 */
class FarmResource extends JsonResource
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
            'type' => $this->type,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'floorsNumber' => $this->floors_number,
            'size' => $this->size,
        ];
    }
}
