<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Support\RelationManagerActivity;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->recordTitleAttribute('name')
                    ->after(function (RelationManager $livewire, array $data) {
                        $record = Role::find($data['recordId'] ?? null);

                        if ($record) {
                            RelationManagerActivity::log('Adjuntado', $livewire->ownerRecord, 'roles', $record);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->after(function (RelationManager $livewire, Model $record) {
                        RelationManagerActivity::log('Desvinculado', $livewire->ownerRecord, 'roles', $record);
                    }),
            ])
            ->bulkActions([
                
            ]);
    }    
}
