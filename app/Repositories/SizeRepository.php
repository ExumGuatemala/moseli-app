<?php

namespace App\Repositories;

use App\Models\Size;

class SizeRepository extends EloquentRepository
{
    public function create(Size $item)
    {
        return $item->save();
    }

}