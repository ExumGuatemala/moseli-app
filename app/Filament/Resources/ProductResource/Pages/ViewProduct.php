<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Support\ResourceViewActivity;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    public function mount($record): void
    {
        parent::mount($record);

        ResourceViewActivity::log(static::$resource, $this->record);
    }

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
