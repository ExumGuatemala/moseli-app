<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Services\OrderService;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;
    protected $listeners = ['refresh'=>'refreshForm'];
    protected static $orderService;

    public function __construct() {
        static::$orderService = new OrderService;
    }

    public function hasCombinedRelationManagerTabsWithForm(): bool
    {
        return true;
    }

    public function refreshForm()
    {
        $this->fillForm();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['total'] = self::$orderService->updateTotal($data['id']);
        return $data;
    }

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()->label('Editar'),
        ];
    }

}
