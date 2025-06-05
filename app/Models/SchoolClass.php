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


    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }


    public function teachers()
    {
        return $this->belongsToMany(Teacher::class , 'class_teachers');
    }
}
