<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Ad extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile(); // Remove this line if you want multiple images
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10);

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->nonOptimized();
    }

    // Helper method to get the first image URL
    public function getFirstImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('images');
    }

    // Helper method to get all image URLs
    public function getImageUrls(): array
    {
        return $this->getMedia('images')->map(function ($media) {
            return $media->getUrl();
        })->toArray();
    }
}