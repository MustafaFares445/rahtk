<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     type="object",
 *     title="Product Resource",
 *     description="Product resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The ID of the product"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The title of the product"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The slug of the product"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The description of the product"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="The price of the product"
 *     ),
 *     @OA\Property(
 *         property="isUrgent",
 *         type="boolean",
 *         description="Indicates if the product is urgent"
 *     ),
 *     @OA\Property(
 *         property="discount",
 *         type="number",
 *         format="float",
 *         description="The discount applied to the product"
 *     ),
 *     @OA\Property(
 *         property="view",
 *         type="integer",
 *         description="The number of views of the product"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string",
 *         description="The address of the product"
 *     ),
 *     @OA\Property(
 *         property="estate",
 *         ref="#/components/schemas/EstateResource",
 *         description="The estate associated with the product"
 *     ),
 *     @OA\Property(
 *         property="school",
 *         ref="#/components/schemas/SchoolResource",
 *         description="The school associated with the product"
 *     ),
 *     @OA\Property(
 *         property="car",
 *         ref="#/components/schemas/CarResource",
 *         description="The car associated with the product"
 *     ),
 *     @OA\Property(
 *         property="electronic",
 *         ref="#/components/schemas/ElectronicResource",
 *         description="The electronic associated with the product"
 *     ),
 *     @OA\Property(
 *         property="farm",
 *         ref="#/components/schemas/FarmResource",
 *         description="The farm associated with the product"
 *     ),
 *     @OA\Property(
 *         property="video",
 *         ref="#/components/schemas/MediaResource",
 *         description="The video associated with the product"
 *     ),
 *     @OA\Property(
 *         property="primaryImage",
 *         ref="#/components/schemas/MediaResource",
 *         description="The primary image associated with the product"
 *     ),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/MediaResource"),
 *         description="The images associated with the product"
 *     ),
 *     @OA\Property(
 *         property="media",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/MediaResource"),
 *         description="All media associated with the product (only included if getAllMedia is true)"
 *     )
 * )
 */
class ProductResource extends JsonResource
{
    private bool $getAllMedia;

    public function __construct($resource ,  bool $getAllMedia = false)
    {
        $this->getAllMedia = $getAllMedia;
        $this->resource = $resource;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'isUrgent' => $this->is_urgent,
            'discount' => $this->discount,
            'view' => $this->view,
            'address' => $this->address,
            'estate' => EstateResource::make($this->whenLoaded('estate')),
            'school' => SchoolResource::make($this->whenLoaded('school')),
            'car' => CarResource::make($this->whenLoaded('car')),
            'electronic' => ElectronicResource::make($this->whenLoaded('electronic')),
            'farm' => FarmResource::make($this->whenLoaded('farm')),
            'primaryImage' => MediaResource::make($this->getFirstMedia('images')),
            'media' => $this->when($this->getAllMedia ,MediaResource::collection($this->getMedia()) )
        ];
    }
}
