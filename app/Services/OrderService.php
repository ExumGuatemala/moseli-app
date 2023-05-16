<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\OrdersProductsRepository;
use App\Models\Order;
use App\Models\Product;
class OrderService
{
    protected $orderRepository;
    protected $ordersProductsRepository;
    public function __construct()
    {
        $this->orderRepository = new OrderRepository(new Order);
        $this->ordersProductsRepository = new OrdersProductsRepository(new Order);
    }

    public static function updateTotal($orderId): string
    {
        $repo = new OrderRepository(new Order);
        $productrepo = new OrdersProductsRepository(new Order);
        $order = $repo->get($orderId)[0];
        $order->total = 0;
        $productsOrder = $productrepo->allForOrder($order->id);
        foreach($productsOrder as $product)
        {
            $order->total += $product->sale_price * $product->pivot->quantity;
        }
        $order->save();
        // self::updateBalance($orderId);
        return strval($order->total);
    }

    public static function updateBalance($orderId): string
    {
        $repo = new OrderRepository(new Order);
        $order = $repo->get($orderId)[0];
        
        $order->balance = 0;
        
        $paymentsOrder = $repo->getOrders($orderId);
        $totalPayed = 0;
        foreach($paymentsOrder as $payment)
        {
            $totalPayed += $payment->amount;
        }
        $order->balance = $order->total - $totalPayed;
        
        $order->save();
        return strval($order->balance);
    }
}
