<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model implements HasMedia
{
    use HasFactory , HasSlug , InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'is_urgent',
        'discount',
        'view',
        'address',
        'type'
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function estate()
    {
        return $this->hasOne(Estate::class);
    }

    public function school()
    {
        return $this->hasOne(School::class);
    }

    public function car()
    {
        return $this->hasOne(Car::class);
    }

    public function electronic()
    {
        return $this->hasOne(Electronic::class);
    }

    public function farm()
    {
        return $this->hasOne(Farm::class);
    }
}
