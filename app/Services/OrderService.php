<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;

class OrderService
{
    public static function addToTotal(Order $order, $productId, $qty)
    {
        $product = Product::find($productId);
        $total = $order->total;
        $total = $total + (float)($product->sale_price * $qty);
        $order->total = $total;
        $order->save();
    }

    public static function substractFromTotal(Order $order, $productId, $qty)
    {
        $product = Product::find($productId);
        $total = $order->total;
        $total = $total - (float)($product->sale_price * $qty);
        $order->total = $total;
        $order->save();
    }
}
