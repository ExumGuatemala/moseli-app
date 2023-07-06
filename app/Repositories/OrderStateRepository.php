<?php

namespace App\Repositories;

use App\Models\OrderState;
use App\Repositories\EloquentRepository;

class OrderStateRepository extends EloquentRepository
{
    public function __construct(OrderState $model)
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
        return $this->model::where('id', $id)->get();
    }

    public function findBy(array $attributes)
    {
        $query = $this->model;

        foreach ($attributes as $key => $value) {
            $query = $query->where($key, $value);
        }

        return $query->first();
    }
}
