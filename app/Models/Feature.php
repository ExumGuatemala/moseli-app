<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;
    protected $table = "features";
    protected $fillable = [
        'name',
        'length',
        'price',
        'corsize',
        'product_types_id'
        // 'price',
    ];

    // public function products()
    // {
    //     return $this->belongsTo(Product::class, 'orders_products', 'order_id', 'product_id')->withPivot('quantity');
    // }
}
