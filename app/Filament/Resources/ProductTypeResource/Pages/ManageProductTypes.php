<?php

namespace App\Filament\Resources\ProductTypeResource\Pages;

use App\Filament\Resources\ProductTypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;

use App\Models\SizeByProduct;
use App\Models\ProductType;

class ManageProductTypes extends ManageRecords
{
    protected static string $resource = ProductTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

   public function generateSizePreset(string $preset): void
{
    $sizes = match ($preset) {
        'shirts' => ['XS','S','M','L','XL','XXL','3XL','4XL'],
        'pants'  => ['28','30','32','34','36','38','40','42','44'],
        default  => [],
    };

    $sizesState = array_map(
        fn ($name, $i) => ['name' => $name, 'sort_order' => $i],
        $sizes,
        array_keys($sizes)
    );

    // 1) Si estás en un Page Action modal (CreateAction)
    if (method_exists($this, 'getMountedActionForm')) {
        $form = $this->getMountedActionForm();

        $state = $form->getState();

        if (! empty($state['sizes'])) {
            Notification::make()
                ->title('Ya hay tallas. Borralas si querés generar de nuevo.')
                ->warning()
                ->send();
            return;
        }

        $state['sizes'] = $sizesState;
        $form->fill($state);

        Notification::make()->title('Tallas generadas')->success()->send();
        return;
    }

    // 2) Si estás en un Table Action modal (Edit desde tabla)
    if (method_exists($this, 'getMountedTableActionForm')) {
        $form = $this->getMountedTableActionForm();

        $state = $form->getState();

        if (! empty($state['sizes'])) {
            Notification::make()
                ->title('Ya hay tallas. Borralas si querés generar de nuevo.')
                ->warning()
                ->send();
            return;
        }

        $state['sizes'] = $sizesState;
        $form->fill($state);

        Notification::make()->title('Tallas generadas')->success()->send();
        return;
    }

    // 3) Fallback (por si tu versión solo tiene $this->form)
    $this->data ??= [];
    if (! empty($this->data['sizes'])) {
        Notification::make()
            ->title('Ya hay tallas. Borralas si querés generar de nuevo.')
            ->warning()
            ->send();
        return;
    }
    $this->data['sizes'] = $sizesState;
    $this->form->fill($this->data);

    Notification::make()->title('Tallas generadas')->success()->send();
}
}