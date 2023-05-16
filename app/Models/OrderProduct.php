<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;
    protected $table = "orders_products";
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity'
    ];

    public function products()
    {
        return $this->belongsTo(Product::class, 'orders_products', 'order_id', 'product_id')->withPivot('quantity');
    }



}
