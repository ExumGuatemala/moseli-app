<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\OrdersProductsRepository;
use App\Models\Order;
use App\Models\Product;
use App\Models\Size;
class SizeService
{
    public function create($name, $length, $feature_id) {
        $size = new Size();
        $size->name = $name;
        $size->length = $length;
        $size->feature_id = $feature_id;
        return $size->save();
    }
}