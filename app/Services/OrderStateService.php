<?php

namespace App\Services;

use App\Models\OrderState;

class OrderStateService
{
    public static function getLastOrderState(): OrderState
    {
        return OrderState::orderBy('process_order', 'desc')->first();
    }
}