<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AdResource",
 *     type="object",
 *     title="Ad Resource",
 *     description="Ad resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The ID of the ad"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The title of the ad"
 *     ),
 *     @OA\Property(
 *         property="image",
 *         ref="#/components/schemas/MediaResource",
 *         description="The image associated with the ad"
 *     )
 * )
 */
class AdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => MediaResource::make($this->getFirstMedia('images')),
        ];
    }
}
