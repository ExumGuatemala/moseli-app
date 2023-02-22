<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\TextInput\Mask;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductType;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralModelLabel = 'Productos';
    protected static ?string $navigationLabel = 'Productos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan('full')
                    ->label("Nombre"),
                TextInput::make('sale_price')
                    ->required()
                    ->mask(fn (TextInput\Mask $mask) => $mask->money(prefix: 'Q.', thousandsSeparator: ',', decimalPlaces: 2))
                    ->label("Precio de Venta"),
                TextInput::make('existence')
                    ->numeric()
                    ->required()
                    ->label("Existencia"),
                Select::make('size')
                    ->label('Talla')
                    ->afterStateHydrated(function (Model|null $record, Select $component) {
                        $record == null ? $component->state(null) : $component->state($record->size);
                    })
                    ->options([
                        '2' => '2',
                        '4' => '4',
                        '6' => '6',
                        '8' => '8',
                        '10' => '10',
                        '12' => '12',
                        '14' => '14',
                        'XS' => 'XS',
                        'S' => 'S',
                        'M' => 'M',
                        'L' => 'L',
                        'XL' => 'XL',
                    ]),
                Select::make('colorId')
                    ->relationship('color', 'name')
                    ->label('Color')
                    ->options(ProductColor::all()->pluck('name', 'id'))
                    ->searchable(),
                Select::make('typeId')
                    ->relationship('type', 'name')
                    ->label('Tipo')
                    ->options(ProductType::all()->pluck('name', 'id'))
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label("Nombre")
                    ->searchable(['name']),
                TextColumn::make('size')
                    ->label('Talla')
                    ->getStateUsing(function (Model $record) {
                        return $record->size;
                    }),
                TextColumn::make('color_id')
                    ->label('Color')
                    ->getStateUsing(function (Model $record) {
                        return $record->color->name;
                    }),
                TextColumn::make('type_id')
                    ->label('Tipo')
                    ->getStateUsing(function (Model $record) {
                        return $record->type->name;
                    }),
                TextColumn::make('sale_price')
                    ->money('gtq', true)
                    ->label("Precio de Venta"),
                TextColumn::make('existence')
                    ->label("Existencia"),
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
            'index' => Pages\ManageProducts::route('/'),
        ];
    }
}
