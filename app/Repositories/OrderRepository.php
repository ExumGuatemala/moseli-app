<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Payment;
use App\Repositories\EloquentRepository;

class OrderRepository extends EloquentRepository
{
    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    // public function all()
    // {
    //     return Quote::all();
    // }

    // public function find(int $id)
    // {
    //     return Quote::find($id);
    // }

    public function get($id)
    {
        return Order::where('id', $id)->get();
    }

    public function getOrders($order_id){
        return Payment::where('order_id', $order_id)->get();
    }

    public function countByKey($key){
        return Order::where('key', $key)->count();
    }
    // public function updateById(int $id, array $attributes): bool
    // {
    //     $obj = Quote::find($id);

    public function updateById(int $id, array $attributes): bool
    {
        $obj = $this->model->find($id);

        foreach ($attributes as $key => $value) {
            $obj->{$key} = $value;
        }

        return $obj->save();
    }
}
