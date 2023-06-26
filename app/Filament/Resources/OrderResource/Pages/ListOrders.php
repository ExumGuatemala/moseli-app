<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Closure;
use App\Filament\Resources\OrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Client;


class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    // protected function getTableQuery(): Builder
    // {
    //     return Order::query()->whereNot('state_id', 2);
    // }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
