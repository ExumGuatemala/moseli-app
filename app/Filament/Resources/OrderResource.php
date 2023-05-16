<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use App\Models\Order;
use App\Models\OrderState;
use App\Models\Client;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-alt';

    protected static ?string $modelLabel = 'Orden';
    protected static ?string $pluralModelLabel = 'Ordenes';
    protected static ?string $navigationLabel = 'Ordenes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('clientId')
                    ->label('Cliente')
                    ->columnSpan('full')
                    ->searchable()
                    ->options(Client::all()->pluck('name', 'id'))
                    ->relationship('client', 'name'),
                TextInput::make('created_at')
                    ->disabled()
                    ->label('Fecha de Creación'),
                Select::make('stateId')
                    ->label('Estado')
                    ->options(OrderState::all()->pluck('name', 'id'))
                    ->relationship('state', 'name'),
                TextInput::make('total')
                    ->default(0)
                    ->mask(fn (TextInput\Mask $mask) => $mask->money(prefix: 'Q.', thousandsSeparator: ',', decimalPlaces: 2)),
                TextInput::make('balance')
                    ->default(0)
                    ->mask(fn (TextInput\Mask $mask) => $mask->money(prefix: 'Q.', thousandsSeparator: ',', decimalPlaces: 2)),
                    // ->hiddenOn('create'),

                Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpan('full')
                    ->rows(10),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('state_id')
                    ->label('Estado')
                    ->getStateUsing(function (Model $record) {
                        return $record->state->name;
                    }),
                TextColumn::make('client_id')
                    ->label('Cliente')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas('client', function (Builder $q) use($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                    })
                    ->getStateUsing(function (Model $record) {
                        return $record->client->name;
                    }),
                TextColumn::make('total')
                    ->money('gtq', true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Fecha de Creación'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\ProductsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
