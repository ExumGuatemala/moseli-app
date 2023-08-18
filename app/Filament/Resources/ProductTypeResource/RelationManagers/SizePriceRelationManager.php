<?php

namespace App\Filament\Resources\ProductTypeResource\RelationManagers;

use Filament\Tables;

use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\RelationManagers\RelationManager;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

use App\Services\SizePriceService;
use App\Enums\ProductEnum;


class SizePriceRelationManager extends RelationManager
{
    protected static string $relationship = 'size_prices';
    protected static ?string $modelLabel = 'Precio de talla';
    protected static ?string $pluralModelLabel = 'Precios de tallas';
    protected static ?string $navigationLabel = 'Precios de tallas';
    protected static ?string $recordTitleAttribute = 'name';

    protected static $sizePriceService;

    public function __construct() {
        static::$sizePriceService = new SizePriceService();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('name')
                    ->label('Talla')
                    ->options(ProductEnum::SIZES),
                        TextInput::make('price')
                            ->label('Precio')
                            ->mask(fn (TextInput\Mask $mask) => $mask->money(prefix: 'Q.', thousandsSeparator: ',', decimalPlaces: 2)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label("Talla"),
                TextColumn::make('price')
                    ->label("Precio")
                    ->money('gtq', true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    
}
