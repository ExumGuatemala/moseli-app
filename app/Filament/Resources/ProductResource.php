<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\TextInput\Mask;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductType;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Pages\ListRecords;
// use ListRecords\Concerns\Translatable;
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralModelLabel = 'Productos';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?string $navigationButton = 'Productos';
    
    public static function form(Form $form): Form
    {
        
        return $form
            ->schema(
                [
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
                Select::make('typeId')
                    ->relationship('type', 'name')
                    ->label('Tipo')
                    ->columnSpan('full')
                    ->options(ProductType::all()->pluck('name', 'id'))
                    ->searchable(),
                Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpan('full')
                    ->rows(3),
                SpatieMediaLibraryFileUpload::make('Imagenes')
                    ->columnSpan('full')
                    ->multiple()
                    ->conversion('thumb')
                    ->enableReordering()
                    ->enableOpen()
                    ->visibility('public'),
            ])
            ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label("Nombre")
                    ->searchable(['name']),
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
                SelectFilter::make('type_id')
                    ->label('Talla')
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
                ])
            ->actions([
                // Actions\LocaleSwitcher::make(),
                Tables\Actions\ViewAction::make()->label('Ver')->modalHeading('Ver Detalles de Producto'),
                Tables\Actions\EditAction::make()->label('Editar')->modalHeading('Editar Producto')->modalButton('Guardar Cambios'),
                Tables\Actions\DeleteAction::make()->label('Eliminar')->modalHeading('Eliminar Producto')
                ->modalSubheading('Esta accion es permanente, desea continuar con la eliminación?')
                ->modalButton('Si, deseo eliminarlo'),
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
