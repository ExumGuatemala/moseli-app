<?php

namespace App\Filament\Resources\CutResource\Pages;

use App\Filament\Resources\CutResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCuts extends ListRecords
{
    protected static string $resource = CutResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
