<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Forms\Components\TextInput;

use Filament\Tables;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;

use Filament\Resources\Resource;
use App\Filament\Resources\InstitutionResource\Pages;
use App\Filament\Resources\InstitutionResource\RelationManagers;
use App\Models\Institution;



use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InstitutionResource extends Resource
{
    protected static ?string $model = Institution::class;

    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $navigationIcon = 'heroicon-o-office-building';

    protected static ?string $modelLabel = 'Institución';
    protected static ?string $pluralModelLabel = 'Instituciones';
    protected static ?string $navigationLabel = 'Instituciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label("Nombre")
                    ->required(),
                TextInput::make('phone')
                    ->label("Teléfono")
                    ->tel()
                    ->required(),
                TextInput::make('address')
                    ->label("Dirección")
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label("Nombre"),
                TextColumn::make('phone')
                    ->label("Teléfono"),
                TextColumn::make('created_at')
                    ->label("Fecha de Creación")
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Ver'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\OrdersRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstitutions::route('/'),
            'create' => Pages\CreateInstitution::route('/create'),
            'view' => Pages\ViewInstitution::route('/{record}'),
            'edit' => Pages\EditInstitution::route('/{record}/edit'),
        ];
    }    
}
