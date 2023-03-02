<?php

namespace App\Filament\Resources\OrderStateResource\Pages;

use App\Filament\Resources\OrderStateResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOrderStates extends ManageRecords
{
    protected static string $resource = OrderStateResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
