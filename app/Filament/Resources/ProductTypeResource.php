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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\SizeByProduct;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Select;
use Illuminate\Support\HtmlString;
use Closure;

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
                    ->columnSpan('full'),

                Repeater::make('features')
                    ->label('Detalles de tipo de producto')
                    ->relationship()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),

                        TextInput::make('size')
                            ->label('Tamaño'),
                    ])
                    ->columnSpan('full'),

                Placeholder::make('size_presets')
                    ->label('') // sin label para que no estorbe
                    ->content(new HtmlString('
        <div class="flex gap-2">
            <button type="button"
                class="px-3 py-2 rounded bg-primary-600 text-white"
                wire:click.prevent="generateSizePreset(\'shirts\')">
                Generar tallas camisas
            </button>

            <button type="button"
                class="px-3 py-2 rounded bg-gray-600 text-white"
                wire:click.prevent="generateSizePreset(\'pants\')">
                Generar tallas pantalón
            </button>
        </div>
        <p class="text-sm text-gray-500 mt-2">
            Esto llena las tallas automáticamente; luego podés reordenarlas.
        </p>
    '))
                    ->hidden(fn () => request()->route()->getName() == 'filament.resources.product-types.view')
                    ->columnSpan('full'),
                Repeater::make('sizes')
                    ->label('Tallas Por Tipo de Producto')
                    ->relationship()
                    ->schema([
                        TextInput::make('name')->label('Talla')->required(),
                        TextInput::make('sort_order')->hidden()->dehydrated(),
                    ])
                    ->defaultItems(0)
                    ->reorderableWithButtons()           // drag & drop
                    ->columnSpan('full')
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProductTypes::route('/'),
        ];
    }
}
