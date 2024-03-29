<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends EloquentRepository
{

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
        return Product::where('id', $id)->get();
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
