<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'quate',
        'working_duration',
        'founding_date',
        'manager',
        'manager_description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($school) {
            // Delete related school classes
            $school->schoolClasses()->each(function ($schoolClass) {
                $schoolClass->delete();
            });

            // Delete related teachers
            $school->teachers()->each(function ($teacher) {
                $teacher->delete();
            });
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function schoolClasses()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    // public function registerMediaCollections(): void
    // {
    //     $this->addMediaCollection('images')
    //         ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
    //         ->singleFile(); // Remove this line if you want multiple images
    // }

    // // Helper method to get the first image URL
    // public function getFirstImageUrl(): ?string
    // {
    //     return $this->getFirstMediaUrl('images');
    // }

    // // Helper method to get all image URLs
    // public function getImageUrls(): array
    // {
    //     return $this->getMedia('images')->map(function ($media) {
    //         return $media->getUrl();
    //     })->toArray();
    // }
}
