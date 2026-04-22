<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Filament\Resources\ProductResource;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Forms\Components\Select;
use App\Models\ProductColor;
use App\Models\ProductType;
use Filament\Forms\Components\Toggle;
use Closure;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\Action;
use Filament\Tables;
use App\Services\OrderService;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\TrashedFilter;
use App\Support\RelationManagerActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected bool $allowsDuplicates = true;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?string $pluralModelLabel = 'Productos';

    protected static $orderService;

    public function __construct() {
        static::$orderService = new OrderService();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...ProductResource::getProductFormSchema(),
                ...static::getOrderProductFields(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre'),
                TextColumn::make('sale_price')
                    ->money('gtq', true)
                    ->label("Precio"),
                TextColumn::make('quantity')
                    ->label("Cantidad"),
                TextColumn::make('colors')
                    ->label("Colores")
                    ->getStateUsing(function (Model $record): String {
                        $result = "";
                        if($record->colors != null) {
                            foreach ($record->colors as $value) {
                                $result = $result . ProductColor::find($value)->name . ', ';
                              }
                        }
                        return $result;
                    })
                    ->wrap(),
                TextColumn::make("size")
                    ->label("Talla"),
                TextColumn::make('subtotal')
                    ->money('gtq', true)
                    ->label("SubTotal")
                    ->getStateUsing(function (Model $record) {
                        return $record->quantity * $record->sale_price;
                    }),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Agregar Producto')
                    ->slideOver()
                    ->modalWidth('4xl')
                    ->modalHeading('Agregar Producto')
                    ->modalButton('Guardar')
                    ->recordSelectOptionsQuery(fn (Builder $query, RelationManager $livewire): Builder => static::scopeProductsForOrder($query, $livewire->ownerRecord))
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        ...static::getOrderProductFields(),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['colors'] = filled($data['colors'] ?? null) ? json_encode($data['colors']) : null;
                        return $data;
                    })
                    ->preloadRecordSelect()
                    ->after(function (RelationManager $livewire, array $data) {  
                        self::$orderService->updateTotal($livewire->ownerRecord->id);
                        self::$orderService->updateBalance($livewire->ownerRecord->id);

                        $record = Product::find($data['recordId'] ?? null);

                        if ($record) {
                            RelationManagerActivity::log('Adjuntado', $livewire->ownerRecord, 'products', $record, [
                                'quantity' => $data['quantity'] ?? null,
                                'size' => $data['size'] ?? null,
                            ]);
                        }

                        $livewire->emit('refresh');
                    }),
                Tables\Actions\CreateAction::make()
                    ->after(function (RelationManager $livewire, ?Model $record) {
                        self::$orderService->updateTotal($livewire->ownerRecord->id);
                        self::$orderService->updateBalance($livewire->ownerRecord->id);

                        if ($record) {
                            RelationManagerActivity::log('Creado', $livewire->ownerRecord, 'products', $record);
                        }

                        $livewire->emit('refresh');
                    }),
                Tables\Actions\CreateAction::make()
                    ->label('Crear Producto')
                    ->slideOver()
                    ->modalWidth('4xl')
                    ->modalHeading('Crear Producto')
                    ->modalButton('Guardar')
                    ->form(fn (RelationManager $livewire): array => [
                        ...static::getOrderCreateProductFields($livewire->ownerRecord),
                        ...static::getOrderProductFields(),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['colors'] = filled($data['colors'] ?? null) ? json_encode($data['colors']) : null;
                        return $data;
                    })
                    ->after(function (RelationManager $livewire) {
                        self::$orderService->updateTotal($livewire->ownerRecord->id);
                        self::$orderService->updateBalance($livewire->ownerRecord->id);
                        $livewire->emit('refresh');
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make("goToProduct")
                        ->icon('heroicon-o-document')
                        ->label("Ver Producto Original")
                        ->action(function (Model $record) {
                            redirect()->intended('/admin/products/'.str($record->id));
                        }),
                    ViewAction::make()
                        ->label("Ver Producto")
                        ->slideOver(),
                    EditAction::make()
                        ->label('Editar Producto')
                        ->slideOver()
                        ->modalWidth('4xl')
                        ->modalHeading('Editar Producto')
                        ->modalButton('Guardar')
                        ->form(fn (EditAction $action): array => [
                            TextInput::make('productName')
                                ->label('Producto')
                                ->disabled()
                                ->afterStateHydrated(function (TextInput $component) use ($action) {
                                    $component->state($action->getRecordTitle());
                                }),
                            TextInput::make('quantity')
                                ->label('Cantidad a comprar')
                                ->required()
                                ->default(1),
                            Select::make('size')
                                ->label('Talla')
                                ->afterStateHydrated(function (Model|null $record, Select $component) {
                                    $record == null ? $component->state(null) : $component->state($record->size);
                                })
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
                                    'XXL' => 'XXL',
                                    '3XL' => '3XL',
                                    '4XL' => '4XL',
                                ]),
                            Select::make('colors')
                                ->multiple()
                                ->label('Color')
                                ->options(ProductColor::all()->pluck('name', 'id')),
                            Toggle::make('has_embroidery')->inline()
                                ->label('Agregar bordado?')
                                ->reactive(),
                            TextInput::make('embroidery')
                                ->label('Texto de Bordado')
                                ->hidden(
                                    fn (Closure $get): bool => $get('has_embroidery') == false
                                ),
                            Toggle::make('has_sublimate')->inline()
                                ->label('Agregar sublimado?')
                                ->reactive(),
                            TextInput::make('sublimate')
                                ->label('Texto de sublimado')
                                ->hidden(
                                    fn (Closure $get): bool => $get('has_sublimate') == false
                                ),
                            Toggle::make('has_special_size')->inline()
                                ->label('Agregar talla especial?')
                                ->reactive(),
                            Textarea::make('special_size')
                                ->label('Detalles de talla especial')
                                ->hidden(
                                    fn (Closure $get): bool => $get('has_special_size') == false
                                ),
                        ])
                        ->mutateFormDataUsing(function (array $data): array {
                            $data['colors'] = filled($data['colors'] ?? null) ? json_encode($data['colors']) : null;
                            return $data;
                        })
                        ->after(function (RelationManager $livewire, Model $record, array $data) {
                            self::$orderService->updateTotal($livewire->ownerRecord->id);
                            self::$orderService->updateBalance($livewire->ownerRecord->id);
                            RelationManagerActivity::log('Actualizado', $livewire->ownerRecord, 'products', $record, [
                                'quantity' => $data['quantity'] ?? null,
                                'size' => $data['size'] ?? null,
                            ]);
                            $livewire->emit('refresh');
                        }),
                    DetachAction::make()
                        ->label('Quitar')
                        ->modalHeading('Quitar de la orden')
                        ->modalSubheading('Esta accion es permanente, desea continuar con la eliminación?')
                        ->modalButton('Si, deseo quitarlo')
                        ->after(function (RelationManager $livewire, Model $record) {
                            self::$orderService->updateTotal($livewire->ownerRecord->id);
                            self::$orderService->updateBalance($livewire->ownerRecord->id);
                            RelationManagerActivity::log('Desvinculado', $livewire->ownerRecord, 'products', $record);
                            $livewire->emit('refresh');
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    protected static function scopeProductsForOrder(Builder $query, Order $order): Builder
    {
        return $query->where('client_id', $order->client_id);
    }

    protected static function getOrderCreateProductFields(Order $order): array
    {
        $schema = ProductResource::getProductFormSchema();

        foreach ($schema as $component) {
            if ($component->getName() === 'client_id') {
                $component
                    ->default($order->client_id)
                    ->disabled()
                    ->dehydrated();
            }

            if ($component->getName() === 'institution_id') {
                $component
                    ->default($order->institution_id)
                    ->disabled()
                    ->dehydrated();
            }
        }

        return $schema;
    }

    protected static function getOrderProductFields(): array
    {
        return [
            TextInput::make('quantity')
                ->label('Cantidad a comprar')
                ->required()
                ->default(1),
            Select::make('size')
                ->label('Talla')
                ->afterStateHydrated(function (Model|null $record, Select $component) {
                    $record == null ? $component->state(null) : $component->state($record->size);
                })
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
                    'XXL' => 'XXL',
                    '3XL' => '3XL',
                    '4XL' => '4XL',
                ]),
            Select::make('colors')
                ->multiple()
                ->label('Color')
                ->options(ProductColor::all()->pluck('name', 'id')),
            Toggle::make('has_embroidery')->inline()
                ->label('Agregar bordado?')
                ->reactive(),
            TextInput::make('embroidery')
                ->label('Texto de Bordado')
                ->hidden(
                    fn (Closure $get): bool => $get('has_embroidery') == false
                ),
            Toggle::make('has_sublimate')->inline()
                ->label('Agregar sublimado?')
                ->reactive(),
            TextInput::make('sublimate')
                ->label('Texto de sublimado')
                ->hidden(
                    fn (Closure $get): bool => $get('has_sublimate') == false
                ),
            Toggle::make('has_special_size')->inline()
                ->label('Agregar talla especial?')
                ->reactive(),
            Textarea::make('special_size')
                ->label('Detalles de talla especial')
                ->hidden(
                    fn (Closure $get): bool => $get('has_special_size') == false
                ),
        ];
    }
}
