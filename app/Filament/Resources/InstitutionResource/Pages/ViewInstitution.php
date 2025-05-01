<?php

namespace App\Filament\Resources\InstitutionResource\Pages;

use App\Filament\Resources\InstitutionResource;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Crypt;
use Filament\Forms\Components\DatePicker;

class ViewInstitution extends ViewRecord
{
    protected static string $resource = InstitutionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
            Action::make('clientView')
                ->label('Reporte por Producto')
                ->action(fn (array $data) => redirect()->route(
                        'institution.orders', 
                        [
                            'institution_hash'  => Crypt::encryptString(strval($this->record->id)),
                            'start_date'      => $data['start_date'],
                            'end_date'          => $data['end_date'],
                        ],
                    )
                )
                ->form([
                    DatePicker::make('start_date')
                        ->label('Fecha Inicial')
                        ->required(),
                    DatePicker::make('end_date')
                        ->label('Fecha Final')
                        ->required(),
                ])
        ];
    }
}
