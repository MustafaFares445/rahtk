<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Exceptions\InvalidConversion;

/**
 * @OA\Schema(
 *     schema="MediaResource",
 *     title="MediaResource",
 *     description="Media resource representation for files and images",
 *     required={"id", "name", "fileName", "collection", "url", "size", "type", "extension", "caption"},
 *
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Unique identifier for the media",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the media file without extension",
 *         example="nature_photo"
 *     ),
 *     @OA\Property(
 *         property="fileName",
 *         type="string",
 *         description="Complete file name with extension",
 *         example="nature_photo.jpg"
 *     ),
 *     @OA\Property(
 *         property="collection",
 *         type="string",
 *         description="Collection name where the media is stored",
 *         example="photos"
 *     ),
 *     @OA\Property(
 *         property="url",
 *         type="string",
 *         format="uri",
 *         description="Full URL to access the media file",
 *         example="https://example.com/storage/media/nature_photo.jpg"
 *     ),
 *     @OA\Property(
 *         property="size",
 *         type="string",
 *         description="Human-readable file size",
 *         example="2.5 MB"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="Type of media",
 *         enum={"image", "video", "document", "audio", "other"},
 *         example="image"
 *     ),
 *     @OA\Property(
 *         property="extension",
 *         type="string",
 *         description="File extension",
 *         example="jpg"
 *     ),
 *     @OA\Property(
 *         property="caption",
 *         type="string",
 *         description="Caption or description of the media. Falls back to name if not set",
 *         example="Beautiful nature photograph"
 *     ),
 *     @OA\Property(
 *         property="width",
 *         type="integer",
 *         format="int32",
 *         description="Width of the image in pixels (only for images)",
 *         example=1920,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="height",
 *         type="integer",
 *         format="int32",
 *         description="Height of the image in pixels (only for images)",
 *         example=1080,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="thumbnailUrl",
 *         type="string",
 *         format="uri",
 *         description="URL of the thumbnail version. Only present if 'thumb' conversion exists",
 *         example="https://example.com/storage/media/conversions/nature_photo-thumb.jpg",
 *         nullable=true
 *     )
 * )
 */
final class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'fileName' => $this->file_name,
            'collection' => $this->collection_name,
            'url' => $this->getFullUrl(),
            'size' => $this->human_readable_size,
            'extension' => $this->extension,
            'type' => $this->getTypeFromExtension(),
            'caption' => $this->getCustomProperty('caption') ?? $this->name,
        ];

        // Check if the 'thumb' conversion exists
        try {
            $thumbnailUrl = $this->getFullUrl('thumb');
            $data['thumbnailUrl'] = $thumbnailUrl;
        } catch (InvalidConversion $e) {
            // If the 'thumb' conversion does not exist, do not add 'thumbnailUrl'
        }

        return $data;
    }
}
