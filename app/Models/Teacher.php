<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'school_id',
        'job_title',
    ];

    protected static function boot()
    {
        parent::boot();

        // When a teacher is deleted, detach from all classes
        static::deleting(function ($teacher) {
            $teacher->schoolClasses()->detach();
        });

        static::addGlobalScope('school', function (Builder $builder) {
            if ($schoolId = request()->input('ownerRecord.id')) {
                $builder->where('school_id', $schoolId);
            }
        });
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function schoolClasses(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class , 'class_teachers');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function getFirstImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('images');
    }

}
