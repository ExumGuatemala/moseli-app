<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductTypeResource\Pages;
use App\Filament\Resources\ProductTypeResource\RelationManagers;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use App\Models\ProductType;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductTypeResource extends Resource
{
    protected static ?string $model = ProductType::class;

    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $modelLabel = 'Tipo de Producto';
    protected static ?string $pluralModelLabel = 'Tipos de Producto';
    protected static ?string $navigationLabel = 'Tipos de Producto';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Nombre')
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('base_price')
                    ->label('Precio Base')
                    ->numeric()
                    ->prefix('Q')
                    ->columnSpanFull(),
                Repeater::make('features')
                ->label('Detalles de tipo de producto')
                    ->relationship()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),

                        TextInput::make('size')
                            ->label('Valor')
                            ->hidden()
                            ->default(''),
                    ])
                    ->columns(1)
                    ->columnSpanFull()
                    // ->addActionLabel('Add Feature')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre'),
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime(),
                TextColumn::make('updated_at')
                    ->label('Fecha de Actualización')
                    ->dateTime(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProductTypes::route('/'),
        ];
    }
}
