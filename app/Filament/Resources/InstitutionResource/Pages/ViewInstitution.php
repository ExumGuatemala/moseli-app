<?php

namespace App\Filament\Resources\InstitutionResource\Pages;

use App\Filament\Resources\InstitutionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInstitution extends ViewRecord
{
    protected static string $resource = InstitutionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
