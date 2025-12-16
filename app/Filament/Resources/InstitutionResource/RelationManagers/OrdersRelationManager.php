<?php

namespace App\Filament\Resources\InstitutionResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;

use Filament\Tables;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;

use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Services\OrderStateService;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $navigationLabel = 'Ordenes';
    protected static ?string $pluralModelLabel = 'Ordenes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_code')
                    ->label("Código"),
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
                TextColumn::make('state_id')
                    ->label('Estado')
                    ->getStateUsing(function (Model $record) {
                        return $record->state->name;
                    }),
                TextColumn::make('created_at')
                    ->label("Fecha de Creación"),
            ])
            ->filters([
                Filter::make('delivered')
                    ->label("Ocultar Entregadas")
                    ->default()
                    ->query(fn (Builder $query): Builder => $query->whereNot('state_id', OrderStateService::getLastOrderState()->id)),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label("Desde"),
                        Forms\Components\DatePicker::make('created_until')
                            ->label("Hasta"),
                    ]),
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Action::make("goToOrder")
                    ->label("Ver Orden")
                    ->action(function (Model $record) {
                        redirect()->intended('/admin/orders/'.str($record->id));
                }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    
}
