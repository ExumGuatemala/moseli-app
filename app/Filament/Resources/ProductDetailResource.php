<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductDetailResource\Pages;
use App\Filament\Resources\ProductDetailResource\RelationManagers;
use App\Models\ProductDetail;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductDetailResource extends Resource
{
    protected static ?string $model = ProductDetail::class;

    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $modelLabel = 'Detalle de Producto';
    protected static ?string $pluralModelLabel = 'Detalles de Producto';
    protected static ?string $navigationLabel = 'Detalles de Producto';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->required()
                    ->relationship('product', 'name')
                    ->label('Producto')
                    ->searchable(['name'])
                    ->preload(),
                Forms\Components\Select::make('institution_id')
                    ->required()
                    ->relationship('institution', 'name')
                    ->label('Institución')
                    ->searchable(['name'])
                    ->preload(),
                Forms\Components\Fieldset::make("Aspectos Generales")->schema([
                    Forms\Components\TextInput::make('fabric_type')
                        ->label('Tipo de Tela'),
                    Forms\Components\TextInput::make('fabric_code')
                        ->label('Código de Tela'),
                    Forms\Components\TextInput::make('lining_type')
                        ->label('Tipo de Forro'),
                    Forms\Components\TextInput::make('lining_color')
                        ->label('Color de Forro'),
                    Forms\Components\TextInput::make('pocket_type')
                        ->label('Tipo de Bolsas'),
                    Forms\Components\TextInput::make('pocket_quantity')
                        ->label('Cantidad de Bolsas'),
                    Forms\Components\TextInput::make('sleeve_type')
                        ->label('Tipo de Mangas'),
                    Forms\Components\TextInput::make('hood_type')
                        ->label('Tipo de Capucha'),
                    Forms\Components\TextInput::make('neckline_type')
                        ->label('Tipo de Cuello'),
                    Forms\Components\TextInput::make('elastic_waist')
                        ->label('Resorte o Topes en cintura'),
                    Forms\Components\TextInput::make('buttons_neckline')
                        ->label('Botones en Cuello de camisa'),
                    Forms\Components\Textarea::make('general_observations')
                        ->label('Observaciones Generales')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(4),
                
                Forms\Components\Fieldset::make("Confección")->schema([
                    Forms\Components\TextInput::make('ziper_position')
                        ->label('Posición de Ziper'),
                    Forms\Components\TextInput::make('ziper_color')
                        ->label('Color de Ziper'),
                    Forms\Components\TextInput::make('resort_color')
                        ->label('Resorte (Especificar Color)'),
                    Forms\Components\TextInput::make('elastic')
                        ->label('Elastico'),
                    Forms\Components\TextInput::make('special_stitching')
                        ->label('Costura Especial'),
                    Forms\Components\TextInput::make('rivets')
                        ->label('Remaches'),
                    Forms\Components\TextInput::make('buttons_color')
                        ->label('Color de Botones'),
                    Forms\Components\TextInput::make('strap_color')
                        ->label('Color de Correa'),
                    Forms\Components\TextInput::make('thread_color')
                        ->label('Color de Hilo'),
                    Forms\Components\TextInput::make('reflective_color')
                        ->label('Color de Reflectivo'),
                    Forms\Components\TextInput::make('reflective_position')
                        ->label('Posición de Reflectivo'),
                    Forms\Components\TextInput::make('reflective_width')
                        ->label('Ancho de Reflectivo'),
                    Forms\Components\TextInput::make('reflective_velcro')
                        ->label('Velcro de Reflectivo'),
                    Forms\Components\TextInput::make('collar_cuff')
                        ->label('Personalizacion de cuello y puño'),
                    Forms\Components\Textarea::make('sewing_observations')
                        ->label('Observaciones de Costura')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(4),

                Forms\Components\Fieldset::make("Confección")->schema([
                    Forms\Components\TextInput::make('personalization_type')
                        ->label('Tipo de Personalización'),
                    Forms\Components\TextInput::make('personalization_size')
                        ->label('Tamaño de Personalización'),
                    Forms\Components\TextInput::make('fabric_background_color')
                        ->label('Color de Fondo de Tela'),
                    Forms\Components\TextInput::make('monogram')
                        ->label('Monograma'),
                    Forms\Components\Repeater::make('logos')
                        ->label('Logos')
                        ->schema([
                            Forms\Components\TextInput::make('logo_position')
                                ->label('Posición del Logo'),
                            Forms\Components\TextInput::make('logo_thread_color')
                                ->label('Color de Hilo'),
                        ])->columns(2)->columnSpanFull(),
                    Forms\Components\Textarea::make('personalization_observations')
                        ->label('Observaciones de Personalización')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Producto')
                    ->sortable(),
                Tables\Columns\TextColumn::make('institution.name')
                    ->label('Institución')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fabric_type')
                    ->label('Tipo de Tela')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fabric_code')
                    ->label('Código de Tela')
                    ->sortable(),
                Tables\Columns\TextColumn::make('personalization_type')
                    ->label('Tipo de Personalización')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductDetails::route('/'),
            'create' => Pages\CreateProductDetail::route('/create'),
            'view' => Pages\ViewProductDetail::route('/{record}'),
            'edit' => Pages\EditProductDetail::route('/{record}/edit'),
        ];
    }    
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
