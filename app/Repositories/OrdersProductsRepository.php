<?php

namespace App\Repositories;
use App\Models\Order;

class OrdersProductsRepository extends EloquentRepository
{
    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function allForOrder($order_id) {

        // return Order::find($order_id)->products;
        return Order::find($order_id)->products;
    }

    // public function all()
    // {
    //     return Quote::all();
    // }
    // public function find(int $id)
    // {
    //     return Quote::find($id);
    // }

    // public function get($id)
    // {
    //     return Quote::where('id', $id)->get();
    // }

    // public function updateById(int $id, array $attributes): bool
    // {
    //     $obj = Quote::find($id);

    //     foreach ($attributes as $key => $value) {
    //         $obj->{$key} = $value;
    //     }

    //     return $obj->save();
    // }
}
