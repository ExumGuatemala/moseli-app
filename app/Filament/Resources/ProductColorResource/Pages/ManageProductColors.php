<?php

namespace App\Filament\Resources\ProductColorResource\Pages;

use App\Filament\Resources\ProductColorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProductColors extends ManageRecords
{
    protected static string $resource = ProductColorResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
