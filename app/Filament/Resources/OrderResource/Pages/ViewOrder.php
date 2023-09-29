<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Pages\Actions\EditAction;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\Model;

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
            EditAction::make()->label('Editar'),
            Action::make("nextStatus")
                ->label(function () {
                    return "Cambiar a " . self::$orderService->getNextOrderStatus($this->record->state_id)->name;
                })
                ->requiresConfirmation()
                ->modalHeading('Cambiar estado')
                ->modalSubheading('¿Seguro que desea cambiar al siguiente estado?')
                ->modalButton('Si, seguro')
                ->action(function () {
                    self::$orderService->changeToNextOrderStatus($this->record->id, $this->record->state_id);
                    redirect()->intended('/admin/orders/'.str($this->record->id));
                }),
            Action::make('getPdf')
                ->label('PDF')
                ->url(fn (): string => route('order.pdf', ['order' => $this->record]), shouldOpenInNewTab: true),
        ];
    }

    //Custom Actions definition
    public function moveToNextOrderStatus(): void
    {
        dd("next status");
    }
}
