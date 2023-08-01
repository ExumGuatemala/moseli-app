<?php

namespace App\Filament\Resources\ProductTypeResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification; 
use App\Models\SizePrice;
use App\Services\SizePriceService;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;


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
                        TextInput::make('price')
                            ->label('Precio')
                            ->mask(fn (TextInput\Mask $mask) => $mask->money(prefix: 'Q.', thousandsSeparator: ',', decimalPlaces: 2)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
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
