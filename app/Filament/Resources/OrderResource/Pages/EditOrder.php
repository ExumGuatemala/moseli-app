<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;
    protected $listeners = ['refresh'=>'refreshForm'];

    public function refreshForm()
    {
        $this->fillForm();
    }

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make()->label('Ver detalles'),
            Actions\DeleteAction::make()->label('Eliminar'),
            Actions\RestoreAction::make()->label('Restaurar'),
        ];
    }

    protected function resolveRecord($key): \Illuminate\Database\Eloquent\Model
    {
        return static::getResource()::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->findOrFail($key);
    }
}
