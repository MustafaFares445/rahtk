<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="EstateResource",
 *     type="object",
 *     title="Estate Resource",
 *     description="Estate resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The ID of the estate"
 *     ),
 *     @OA\Property(
 *         property="rooms",
 *         type="integer",
 *         description="Number of rooms in the estate"
 *     ),
 *     @OA\Property(
 *         property="area",
 *         type="number",
 *         format="float",
 *         description="Area of the estate in square meters"
 *     ),
 *     @OA\Property(
 *         property="floorsNumber",
 *         type="integer",
 *         description="Number of floors in the estate"
 *     ),
 *     @OA\Property(
 *         property="isFurnished",
 *         type="boolean",
 *         description="Indicates if the estate is furnished"
 *     ),
 *     @OA\Property(
 *         property="floor",
 *         type="integer",
 *         description="Floor number of the estate"
 *     )
 * )
 */
class EstateResource extends JsonResource
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
            'rooms' => $this->rooms,
            'area' => $this->area,
            'floorsNumber' => $this->floors_number,
            'isFurnished' => $this->is_furnished,
            'floor' => $this->floor,
        ];
    }
}
