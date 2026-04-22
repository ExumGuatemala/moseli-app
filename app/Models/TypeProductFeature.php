<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeProductFeature extends Model
{
    protected $fillable = [
        'product_type_id',
        'name',
        'size',
    ];

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }
}
