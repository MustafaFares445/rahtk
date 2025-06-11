<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'rooms',
        'area',
        'floors_number',
        'is_furnished',
        'floor',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_furnished' => 'boolean',
        'rooms' => 'integer',
        'area' => 'decimal:2',
        'floors_number' => 'integer',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'is_furnished' => false,
    ];

    /**
     * Get the product that owns the estate.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}