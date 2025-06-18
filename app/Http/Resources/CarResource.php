<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CarResource",
 *     type="object",
 *     title="Car Resource",
 *     description="Car resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The ID of the car"
 *     ),
 *     @OA\Property(
 *         property="model",
 *         type="string",
 *         description="The model of the car"
 *     ),
 *     @OA\Property(
 *         property="year",
 *         type="integer",
 *         description="The manufacturing year of the car"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="strign",
 *         description="The type of the car (sell)"
 *     )
 * )
 */
class CarResource extends JsonResource
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
            'model' => $this->model,
            'year' => $this->year,
        ];
    }
}