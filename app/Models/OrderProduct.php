<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "orders_products";
    protected $fillable = [
        'order_id',
        'product_id',
        'size',
        'embroidery',
        'sublimate',
        'quantity'
    ];

    protected $casts = [
        'colors' => 'array',
        'has_embroidery' => 'boolean',
        'has_sublimate' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsTo(Product::class, 'orders_products', 'order_id', 'product_id')->withPivot('id', 'quantity', 'sublimate','size','embroidery','has_embroidery','has_sublimate', 'colors');
    }
}
