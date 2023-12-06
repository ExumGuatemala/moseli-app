<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Closure;
use App\Filament\Resources\OrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\OrderState;
use App\Enums\OrderEnum;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Client;


class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getTableQuery(): Builder
    {
        $deliveredOrderStateId = OrderState::where('name', OrderEnum::DELIVERED)->get()[0]->id;
        return Order::query()
            ->whereNot('state_id', $deliveredOrderStateId)
            ->where('institution_id', null);
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
