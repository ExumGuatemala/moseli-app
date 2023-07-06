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
        'size',
        'colors',
        'has_embroidery',
        'embroidery',
        'has_sublimate',
        'sublimate',
        'quantity'
    ];

    protected $casts = [
        'colors' => 'array',
        'has_embroidery' => 'boolean',
        'has_sublimate' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'orders_products', 'order_id', 'product_id');
    }
}
