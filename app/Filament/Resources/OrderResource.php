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
use App\Models\Branch;
use App\Models\Client;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Repositories\OrderStateRepository;
use App\Repositories\ProductRepository;
use App\Services\OrderService;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-alt';

    protected static ?string $modelLabel = 'Orden';
    protected static ?string $pluralModelLabel = 'Ordenes';
    protected static ?string $navigationLabel = 'Ordenes';
    protected static ?string $buttonLabel = 'Ordenes'; 
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('clientId')
                    ->label('Cliente')
                    ->columnSpan('full')
                    ->searchable()
                    ->options(Client::all()->pluck('name', 'id'))
                    ->relationship('client', 'name')
                    ->required(),
                TextInput::make('created_at')
                    ->disabled()
                    ->label('Fecha de Creación'),
                Select::make('stateId')
                    ->label('Estado')
                    ->options(OrderState::all()->pluck('name', 'id'))
                    ->relationship('state', 'name')
                    ->required(),
                Select::make('branchId')
                    ->label('Sucursal')
                    ->options(Branch::all()->pluck('name', 'id'))
                    ->relationship('branch', 'name')
                    ->required(),
                TextInput::make('key')
                    ->label("Código")
                    ->disabled()
                    ->afterStateHydrated(function (TextInput $component, $state) {
                        if(!$state){
                            $component->state(strtoupper(substr(bin2hex(random_bytes(ceil(8 / 2))), 0, 8)));
                        }
                    }),                    
                TextInput::make('total')
                    ->default(0)
                    ->mask(fn (TextInput\Mask $mask) => $mask->money(prefix: 'Q.', thousandsSeparator: ',', decimalPlaces: 2)),
                TextInput::make('balance')
                    ->label("Saldo")
                    ->default(0)
                    ->mask(fn (TextInput\Mask $mask) => $mask->money(prefix: 'Q.', thousandsSeparator: ',', decimalPlaces: 2)),
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
                TextColumn::make('key')
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
                TextColumn::make('key')
                    ->label("Código")
                    ->searchable(['key']),
                TextColumn::make('total')
                    ->money('gtq', true),
                TextColumn::make('branch_id')
                    ->label('Sucursal')
                    ->getStateUsing(function (Model $record) {
                        return $record->branch->name;
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Fecha de Creación'),
            ])
            ->filters([
                SelectFilter::make('client_id')
                    ->label('Clientes')
                    ->options(
                        Client::get()->pluck('name', 'id')
                    ),
                SelectFilter::make('state_id')
                    ->label('Estado')
                    ->multiple()
                    ->options(
                        OrderState::get()->pluck('name', 'id')
                    ),
                Filter::make('noEntregados')
                    ->label('No mostrar entregados')
                    ->query(fn (Builder $query): Builder => $query->whereNot('state_id', OrderState::where('name', 'Entregado')->first()->id))
                    ->default(true)
            ])
            ->actions([
                Action::make("nextStatus")
                    ->label(function (Model $record) {
                        $orderService = new OrderService();
                        return "Cambiar a " . $orderService->getNextOrderStatus($record->state_id)->name;
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Cambiar estado')
                    ->modalSubheading('¿Seguro que desea cambiar al siguiente estado?')
                    ->modalButton('Si, seguro')
                    ->action(function (Model $record) {
                        $orderService = new OrderService();
                        $orderService->changeToNextOrderStatus($record->id, $record->state_id);
                    }),
                Tables\Actions\ViewAction::make()->label('Ver'),
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
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
