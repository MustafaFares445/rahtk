<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolClass extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'school_id',
    ];

    protected static function boot()
    {
        parent::boot();

        // When a school class is deleted, detach all teachers
        static::deleting(function ($schoolClass) {
            $schoolClass->teachers()->detach();
        });
    }


    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }


    public function teachers()
    {
        return $this->belongsToMany(Teacher::class , 'class_teachers');
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

        $this->addMediaCollection('videos')
            ->singleFile();
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
