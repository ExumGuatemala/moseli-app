<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class RelationManagerActivity
{
    public static function log(
        string $action,
        Model $ownerRecord,
        string $relationship,
        ?Model $subject = null,
        array $properties = [],
        ?string $description = null
    ): void {
        $subject ??= $ownerRecord;

        activity()
            ->useLog('Relation Manager')
            ->causedBy(auth()->user())
            ->performedOn($subject)
            ->event($action)
            ->withProperties(array_merge([
                'relationship' => $relationship,
                'owner_type' => $ownerRecord::class,
                'owner_id' => $ownerRecord->getKey(),
                'owner_label' => self::labelFor($ownerRecord),
                'subject_type' => $subject::class,
                'subject_id' => $subject->getKey(),
                'subject_label' => self::labelFor($subject),
            ], $properties))
            ->log($description ?? self::defaultDescription($action, $relationship, $ownerRecord, $subject));
    }

    private static function labelFor(Model $record): string
    {
        return class_basename($record) . ' # ' . $record->getKey();
    }

    private static function defaultDescription(string $action, string $relationship, Model $ownerRecord, Model $subject): string
    {
        return strtolower(class_basename($subject)) . ' ' . strtolower($action) .
            ' en relacion ' . $relationship .
            ' de ' . self::labelFor($ownerRecord);
    }
}
