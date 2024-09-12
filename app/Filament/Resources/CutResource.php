<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CutResource\Pages;
use App\Filament\Resources\CutResource\RelationManagers;
use App\Models\Cut;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class CutResource extends Resource
{
    protected static ?string $model = Cut::class;

    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?string $navigationIcon = 'heroicon-o-scissors';

    protected static ?string $modelLabel = 'Corte';
    protected static ?string $pluralModelLabel = 'Cortes';
    protected static ?string $navigationLabel = 'Cortes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_id')
                    ->label("ID de Orden"),
                Forms\Components\TextInput::make('state')
                    ->label("Estado"),
                Forms\Components\DateTimePicker::make('start_date')
                    ->label("Fecha de Inicio")
                    ->disabled(),
                Forms\Components\DateTimePicker::make('end_date')
                    ->label("Fecha de Finalización"),
                Forms\Components\Textarea::make('description')
                    ->label("Descripción")
                    ->columnSpan('full'),
                SpatieMediaLibraryFileUpload::make('Imagenes')
                    ->columnSpan('full')
                    ->multiple()
                    ->conversion('thumb')
                    ->enableReordering()
                    ->enableOpen()
                    ->visibility('public'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->label("ID de Orden"),
                Tables\Columns\TextColumn::make('client')
                    ->label("Cliente/Institución")
                    ->getStateUsing(function (Model $record) {
                        return $record->order->institution->name? $record->order->client->name . " / " . $record->order->institution->name : $record->order->client->name;
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->label("Fecha de Inicio")
                    ->dateTime(),
                Tables\Columns\TextColumn::make('state')
                    ->label("Estado"),
                Tables\Columns\TextColumn::make('created_at')
                    ->label("Fecha de Creación")
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCuts::route('/'),
            'create' => Pages\CreateCut::route('/create'),
            'view' => Pages\ViewCut::route('/{record}'),
            'edit' => Pages\EditCut::route('/{record}/edit'),
        ];
    }    
}
