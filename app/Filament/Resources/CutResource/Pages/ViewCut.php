<?php

namespace App\Filament\Resources\CutResource\Pages;

use App\Filament\Resources\CutResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCut extends ViewRecord
{
    protected static string $resource = CutResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
