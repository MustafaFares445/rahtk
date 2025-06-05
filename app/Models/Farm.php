<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Farm extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'type',
        'bedrooms',
        'bathrooms',
        'floors_number',
        'size',
    ];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
