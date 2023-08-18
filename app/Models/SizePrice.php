<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SizePrice extends Model
{
    use HasFactory;
    protected $table = 'size_prices';

    protected $fillable = [
        'name',
        'price',
        'product_type_id'
    ];
}
