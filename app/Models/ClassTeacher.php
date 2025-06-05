<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassTeacher extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'teacher_id',
        'class_id',
    ];


    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function shcoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }
}
