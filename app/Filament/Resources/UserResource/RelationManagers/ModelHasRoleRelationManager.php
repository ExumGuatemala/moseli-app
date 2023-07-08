<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ModelHasRoleRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('role_id')
                    ->required()
                    ->label('Rol')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('role_id')
                ->label('Nombre de rol')
                ->getStateUsing(function (Model $record) {
                    return $record->name;
                }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make('name')
                ->label('Asignar un rol')
                ->modalHeading('Asignar un rol')
                ->modalButton('Guardar'),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                ->label('Desasignar')
                    ->modalHeading('Desasignar rol')
                    ->modalSubheading('Al momento de desasignar, el usuario perderá sus permisos, puede volver a vincular el rol al usuario si lo desea')
                    ->modalButton('Si, deseo desasignar este rol'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    
}
