<?php

namespace App\Support;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class ResourceViewActivity
{
    public static function log(string $resourceClass, Model $record): void
    {
        $user = auth()->user();
        $modelLabel = method_exists($resourceClass, 'getModelLabel')
            ? $resourceClass::getModelLabel()
            : strtolower(class_basename($record));

        $description = $modelLabel . ' visto';

        if ($user) {
            $description .= ' por ' . Filament::getUserName($user);
        }

        activity()
            ->useLog(config('filament-logger.resources.log_name'))
            ->causedBy($user)
            ->performedOn($record)
            ->event('Visto')
            ->log($description);
    }
}
