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

    public function getThePriceOfAProduct($features, $prices){
        $array_given = [];
        $result = [];
        foreach ($features as $value) {
            $array_given = [];
            array_push($array_given, intval($value['sizes']));
            foreach ($value['sizis'] as $item) {
                array_push($array_given, $item["length"]);
            }
            sort($array_given);
            $biggest_size = $array_given[array_search(intval($value['sizes']),$array_given)+1] ?? $array_given[array_search(intval($value['sizes']),$array_given)];
            foreach ($value['sizis'] as $item) {
                if ($item["length"] == $biggest_size) {
                    array_push($result, $prices->where('name',$item['name'])->first()->price ?? 0   );
                } 
            }
        }
        rsort($result);
        return $result[0];
    }


}
