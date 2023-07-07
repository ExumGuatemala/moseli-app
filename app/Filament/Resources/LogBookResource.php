<?php

namespace App\Filament\Resources;


use Filament\Tables\Filters\Filter;

use App\Filament\Resources\LogBookResource\Pages;
use App\Filament\Resources\LogBookResource\RelationManagers;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Models\LogBook;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LogBookResource extends Resource
{
    protected static ?string $model = LogBook::class;
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $modelLabel = 'Bitacora';
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $pluralModelLabel = 'Bitacora';
    protected static ?string $navigationLabel = 'Bitacora';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('type')
                    ->required()
                    ->label('Tipo'),
                TextInput::make('description')
                    ->required()
                    ->label('Descripcion'),
                TextInput::make('user_id')
                    ->required()
                    ->label('Autor'),
                TextInput::make('created_at')
                    ->required()
                    ->label('fecha'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('tipo')
                    ->getStateUsing(function (Model $record) {
                        if ($record->type == "App\Models\Order" ){
                            return "Orden";
                        }
                        else
                        {
                            return "Otro tipo";
                        }
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Fecha de Realizacion'),
                TextColumn::make('description')
                    ->label('Descripcion')
                    ->searchable(['description']),
                TextColumn::make('user_id')
                    ->label('Autor')
                    ->getStateUsing(function (Model $record) {
                        return $record->user->name;
                    }),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(
                        [
                            "App\Models\Order"=>"Orden"
                        ]
                    ),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                SelectFilter::make('user_id')
                    ->label('Usuarios')
                    ->options(
                        User::get()->pluck('name', 'id')
                    ),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
                ->label('Ver detalles'),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
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

            'index' => Pages\ListLogBooks::route('/'),
            'create' => Pages\CreateLogBook::route('/create'),
            'edit' => Pages\EditLogBook::route('/{record}/edit'),
        ];
    }
}
