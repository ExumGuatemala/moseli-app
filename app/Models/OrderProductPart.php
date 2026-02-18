<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductPart extends Model
{
    protected $fillable = [
        'order_product_id',
        'product_part_id',
        'size',
        'color_id',
    ];

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function productPart()
    {
        return $this->belongsTo(ProductPart::class);
    }

    public function color()
    {
        return $this->belongsTo(\App\Models\ProductColor::class, 'color_id');
    }
}
