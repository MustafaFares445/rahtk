<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected $casts = [
        'area' => 'double'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
