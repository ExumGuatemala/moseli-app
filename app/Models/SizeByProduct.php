<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeByProduct extends Model
{
    protected $table = 'sizes_by_product';

    protected $fillable = [
        'product_type_id',
        'name',
        'sort_order',
    ];

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }
}
