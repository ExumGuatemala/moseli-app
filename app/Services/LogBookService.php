<?php

namespace App\Services;

use App\Repositories\LogBookRepository;
use App\Repositories\OrdersProductsRepository;
use App\Models\LogBook;
class LogBookService
{
    protected $logBookRepository;

    public function saveEvent($model_id, $model, $user_id, $description){
        $newLogBook = new LogBook();
        $newLogBook->type = $model;
        $newLogBook->model_id = $model_id;
        $newLogBook->description = $description;
        $newLogBook->user_id = $user_id; 
        $newLogBook->save();
    }
}
