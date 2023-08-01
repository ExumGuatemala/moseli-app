<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $recordTitleAttribute = 'key';
    protected static ?string $navigationLabel = 'Ordenes';
    protected static ?string $pluralModelLabel = 'Ordenes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key'),
                TextColumn::make('total')
                    ->money('gtq', true),
                TextColumn::make('branch_id')
                    ->label('Sucursal')
                    ->getStateUsing(function (Model $record) {
                        return $record->branch->name;
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Fecha de Creación'),
            ])
            ->filters([
                //
            ])
            ->headerActions([

            ])
            ->actions([
                Action::make("goToOrder")
                    ->label("Ver Orden")
                    ->action(function (Model $record) {
                        redirect()->intended('/admin/orders/'.str($record->id));
                    }),
            ])
            ->bulkActions([

            ]);
    }    
}
