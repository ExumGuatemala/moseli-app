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

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('fabric_type'),
                Forms\Components\TextInput::make('fabric_code'),
                Forms\Components\TextInput::make('lining_color'),
                Forms\Components\TextInput::make('lining_type'),
                Forms\Components\TextInput::make('pocket_type'),
                Forms\Components\TextInput::make('pocket_quantity'),
                Forms\Components\TextInput::make('sleeve_type'),
                Forms\Components\TextInput::make('hood_type'),
                Forms\Components\TextInput::make('neckline_type'),
                Forms\Components\TextInput::make('elastic_waist'),
                Forms\Components\TextInput::make('buttons_neckline'),
                Forms\Components\TextInput::make('ziper_position'),
                Forms\Components\TextInput::make('ziper_color'),
                Forms\Components\TextInput::make('resort_color'),
                Forms\Components\TextInput::make('elastic'),
                Forms\Components\TextInput::make('special_stitching'),
                Forms\Components\TextInput::make('rivets'),
                Forms\Components\TextInput::make('buttons_color'),
                Forms\Components\TextInput::make('strap_color'),
                Forms\Components\TextInput::make('thread_color'),
                Forms\Components\TextInput::make('reflective_color'),
                Forms\Components\TextInput::make('reflective_position'),
                Forms\Components\TextInput::make('reflective_width'),
                Forms\Components\TextInput::make('reflective_velcro'),
                Forms\Components\TextInput::make('collar_cuff'),
                Forms\Components\Textarea::make('general_observations')
                    ->rows(3),
                Forms\Components\Textarea::make('sewing_observations')
                    ->rows(3),
                Forms\Components\TextInput::make('personalization_type'),
                Forms\Components\TextInput::make('personalization_size'),
                Forms\Components\TagsInput::make('logos'),
                Forms\Components\TextInput::make('fabric_background_color'),
                Forms\Components\TextInput::make('monogram'),
                Forms\Components\Textarea::make('personalization_observations')
                    ->rows(3),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name'),
                Forms\Components\Select::make('institution_id')
                    ->relationship('institution', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fabric_type'),
                Tables\Columns\TextColumn::make('fabric_code'),
                Tables\Columns\TextColumn::make('lining_color'),
                Tables\Columns\TextColumn::make('pocket_type'),
                Tables\Columns\TextColumn::make('sleeve_type'),
                Tables\Columns\TextColumn::make('product.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('institution.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
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
