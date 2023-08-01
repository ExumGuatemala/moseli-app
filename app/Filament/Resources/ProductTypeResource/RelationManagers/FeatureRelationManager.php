<?php

namespace App\Filament\Resources\ProductTypeResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use App\Filament\Resources\TextInput\Mask;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use App\Services\SizeService;


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
                Forms\Components\TextInput::make('name')
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
                        TextInput::make('length')
                            ->label('Tamaño (en cm)'),

                    ])
                    ->itemLabel("talla")
                    // ->orderable()
                    ->columnSpan('full')
                    ->createItemButtonLabel('Añadir una talla')
                    ->columns(1)
                    ->defaultItems(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label('Nombre'),
                Tables\Columns\TextColumn::make('corsize')
                ->label('Talla'),
                Tables\Columns\TextColumn::make('length')
                ->label('Tamaño'),
                Tables\Columns\TextColumn::make('price')
                ->label('Precio'),
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
