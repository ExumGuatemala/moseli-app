<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\OrdersProductsRepository;
use App\Models\Order;
use App\Models\Product;
class ProductOrderService
{
    // protected $orderRepository;
    protected $ordersProductsRepository;
    public function __construct()
    {
        // $this->orderRepository = new OrderRepository(new Order);
        $this->ordersProductsRepository = new OrdersProductsRepository(new Order);
    }

    public function deleteOrderProductByProductId($order_id, $product_id, $quantity) {
        return $this->ordersProductsRepository->getOrderProductId($order_id, $product_id, $quantity);
    }
}
