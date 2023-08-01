<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;

    protected $table = 'product_types';

    protected $fillable = [
        'name',
    ];

    public function features()
    {
        return $this->hasMany(Feature::class, 'product_types_id');
    }
    public function size_prices()
    {
        return $this->hasMany(SizePrice::class, 'product_type_id');
    }
}
