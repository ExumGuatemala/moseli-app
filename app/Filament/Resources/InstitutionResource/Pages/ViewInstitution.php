<?php

namespace App\Filament\Resources\InstitutionResource\Pages;

use App\Filament\Resources\InstitutionResource;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Crypt;

class ViewInstitution extends ViewRecord
{
    protected static string $resource = InstitutionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
            Action::make('clientView')
                ->label('Vista Pública')
                ->url(fn (): string => route('institution.orders', ['institution_hash' => Crypt::encryptString(strval($this->record->id))]), shouldOpenInNewTab: true),
        ];
    }
}
