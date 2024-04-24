<?php

namespace App\Filament\Resources\CutResource\Pages;

use App\Filament\Resources\CutResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCut extends EditRecord
{
    protected static string $resource = CutResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
