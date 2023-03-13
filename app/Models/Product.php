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
        'size',
        'colors',
    ];

    protected $casts = [
        'colors' => 'array',
    ];

    /**
     * Get the color for the product.
     */
    public function type()
    {
        return $this->belongsTo(ProductType::class);
    }

    /**
     * The products that belong to the Product.
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'orders_products', 'order_id', 'product_id')->withPivot('quantity');
    }
}
