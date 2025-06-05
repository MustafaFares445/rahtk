<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ElectronicResource",
 *     type="object",
 *     title="Electronic Resource",
 *     description="Electronic resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The ID of the electronic item"
 *     ),
 *     @OA\Property(
 *         property="model",
 *         type="string",
 *         description="The model of the electronic item"
 *     ),
 *     @OA\Property(
 *         property="brand",
 *         type="string",
 *         description="The brand of the electronic item"
 *     ),
 *     @OA\Property(
 *         property="year",
 *         type="integer",
 *         description="The year of the electronic item"
 *     )
 * )
 */
class ElectronicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'model' => $this->model,
            'brand' => $this->brand,
            'year' => $this->year,
        ];
    }
}