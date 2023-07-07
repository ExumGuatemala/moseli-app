<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Services\OrderService;

class CreateOrder extends CreateRecord
{
    protected static $orderService;
    public function __construct() {
        static::$orderService = new OrderService();
    }
    protected static string $resource = OrderResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['key'] = self::$orderService->setAKey($data['key']);
        return $data;
    }
}
