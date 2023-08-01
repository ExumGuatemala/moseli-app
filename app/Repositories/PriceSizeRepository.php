<?php

namespace App\Repositories;

use App\Models\Product;

class PriceSizeRepository extends EloquentRepository
{

    // public function all()
    // {
    //     return Quote::all();
    // }

    public function create(SizePrice $item)
    {
        return $item->save();
    }

    public function get($id)
    {
        return Product::where('id', $id)->get();
    }
    public function getOne($id)
    {
        return Product::where('id', $id)->first()->name;
    }

    // public function updateById(int $id, array $attributes): bool
    // {
    //     $obj = Quote::find($id);

    //     foreach ($attributes as $key => $value) {
    //         $obj->{$key} = $value;
    //     }

    //     return $obj->save();
    // }
}