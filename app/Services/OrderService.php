<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\OrdersProductsRepository;

class OrderService
{
    protected $orderRepository;
    protected $productRepository;
    protected $ordersProductsRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository;
        $this->productRepository = new ProductRepository;
        $this->ordersProductsRepository = new OrdersProductsRepository;
    }

    public function updateTotal($orderId): string
    {
        $order = $this->orderRepository->get($orderId)[0];
        $order->total = 0;
        $products = $this->ordersProductsRepository->allForOrder($order->id);
        foreach($products as $product)
        {
            //dd($this->productRepository->get($product->id));
            //$order->total += $this->productRepository->get($product->id)->sale_price * $product->quantity;
        }
        $order->save();
        return strval($order->total);
    }

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
