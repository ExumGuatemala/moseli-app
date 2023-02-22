<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'existence',
        'order',
        'sale_price',
    ];

    /**
     * Get the color for the product.
     */
    public function color()
    {
        return $this->belongsTo(ProductColor::class);
    }

    /**
     * Get the color for the product.
     */
    public function type()
    {
        return $this->belongsTo(ProductType::class);
    }
}
