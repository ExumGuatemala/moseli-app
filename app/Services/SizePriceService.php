<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\OrdersProductsRepository;
use App\Models\Order;
use App\Models\Product;
use App\Models\SizePrice;
class SizePriceService
{
    // protected $orderRepository;
    // protected $ordersProductsRepository;
    // public function __construct()
    // {
    //     $this->orderRepository = new OrderRepository(new Order);
    //     $this->ordersProductsRepository = new OrdersProductsRepository(new Order);
    // }

    public function create($name,$price, $id) {
        $sizePrice = new SizePrice();
        $sizePrice->name = $name;
        $sizePrice->price = $price;
        $sizePrice->product_type_id = $id;
        return $sizePrice->save();
        // return $this->ordersProductsRepository->getOrderProductId($order_id, $product_id, $quantity);
    }
}