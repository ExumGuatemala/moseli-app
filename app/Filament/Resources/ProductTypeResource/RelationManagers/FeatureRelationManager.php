<?php

namespace App\Filament\Resources\ProductTypeResource\RelationManagers;

use Filament\Tables;

use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;

use Illuminate\Database\Eloquent\Model;

use App\Services\SizeService;
use App\Enums\ProductEnum;


class FeatureRelationManager extends RelationManager
{
    protected static string $relationship = 'features';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Caracteristica';
    protected static ?string $pluralModelLabel = 'Caracteristicas';
    protected static ?string $navigationLabel = 'Caracteristicas';

    protected static $sizeService;

    public function __construct() {
        static::$sizeService = new SizeService();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->columnSpan('full')
                    ->required()
                    ->maxLength(255),
                Repeater::make('sizes')
                    ->relationship()
                    ->label("Tallas")
                    ->schema([
                        Select::make('name')
                            ->label('Talla')
                            ->options(ProductEnum::SIZES),
                        TextInput::make('length')
                            ->label('Tamaño (en cm)'),

                    ])
                    ->itemLabel(fn (array $state): ?string => "Talla " . $state['name'] ?? null)
                    ->columnSpan('full')
                    ->createItemButtonLabel('Añadir una talla')
                    ->columns(1)
                    ->collapsed()
                    ->defaultItems(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label('Nombre'),
                TextColumn::make('availableSizes')
                ->label('Tallas con Informacion')
                ->getStateUsing(function (Model $record): String {
                    return implode(", ", $record->sizes->pluck('name')->toArray());
                }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->before(function (array $data, RelationManager $livewire) {
                    $data['product_types_id'] = $livewire->ownerRecord->id;
                    return $data;
                })
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
