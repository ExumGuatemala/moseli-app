<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderStateResource\Pages;
use App\Filament\Resources\OrderStateResource\RelationManagers;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Models\OrderState;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderStateResource extends Resource
{
    protected static ?string $model = OrderState::class;

    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $modelLabel = 'Estado de Orden';
    protected static ?string $pluralModelLabel = 'Estados de Orden';
    protected static ?string $navigationLabel = 'Estados de Orden';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Nombre'),
                TextInput::make('process_order')
                    ->label('Orden en Proceso')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre'),
                TextColumn::make('process_order')
                    ->label('Orden en Proceso'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Fecha de Creación'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOrderStates::route('/'),
        ];
    }
}
