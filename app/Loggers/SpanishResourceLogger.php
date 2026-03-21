<?php

namespace App\Loggers;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Z3d0X\FilamentLogger\Loggers\ResourceLogger;

class SpanishResourceLogger extends ResourceLogger
{
    public function created(Model $model)
    {
        $this->logInSpanish($model, 'Creado', $model->getAttributes());
    }

    public function updated(Model $model)
    {
        $changes = $model->getChanges();

        if (count($changes) === 1 && array_key_exists('remember_token', $changes)) {
            return;
        }

        $this->logInSpanish($model, 'Actualizado', $changes);
    }

    public function deleted(Model $model)
    {
        $this->logInSpanish($model, 'Eliminado');
    }

    public function restored(Model $model)
    {
        $this->logInSpanish($model, 'Restaurado');
    }

    private function logInSpanish(Model $model, string $event, ?array $attributes = null): void
    {
        $description = $this->getModelLabel($model) . ' ' . strtolower($event);

        if (auth()->check()) {
            $description .= ' por ' . $this->getUserName(auth()->user());
        }

        $this->activityLogger()
            ->event($event)
            ->performedOn($model)
            ->withProperties($this->getLoggableAttributes($model, $attributes))
            ->log($description);
    }

    private function getModelLabel(Model $model): string
    {
        foreach (Filament::getResources() as $resource) {
            if ($resource::getModel() === $model::class) {
                return $resource::getModelLabel();
            }
        }

        return strtolower($this->getModelName($model));
    }
}
