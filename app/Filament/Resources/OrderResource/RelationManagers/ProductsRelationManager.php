<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Closure;
use Illuminate\Database\Eloquent\Model;

use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;

use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;

use App\Models\ProductColor;
use App\Models\ProductType;

use App\Models\ProductPart;
use App\Models\OrderProduct;
use App\Models\OrderProductPart;

use App\Services\OrderService;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected bool $allowsDuplicates = true;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?string $pluralModelLabel = 'Productos';

    protected static $orderService;

    public function __construct()
    {
        static::$orderService = new OrderService();
    }

    private static function sizeOptions(): array
    {
        return [
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
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->columnSpan('full')
                ->label("Nombre"),
            TextInput::make('sale_price')
                ->required()
                ->mask(fn(TextInput\Mask $mask) => $mask->money(prefix: 'Q.', thousandsSeparator: ',', decimalPlaces: 2))
                ->label("Precio de Venta"),
            TextInput::make('existence')
                ->numeric()
                ->label("Existencia")
                ->afterStateHydrated(function (TextInput $component, $state) {
                    if (!$state) $component->state(1);
                }),
            Select::make('typeId')
                ->relationship('type', 'name')
                ->label('Tipo')
                ->columnSpan('full')
                ->options(ProductType::all()->pluck('name', 'id'))
                ->required()
                ->searchable(),
            Textarea::make('description')
                ->label('Descripción')
                ->columnSpan('full')
                ->rows(3),
            TextInput::make('quantity')
                ->label('Cantidad a comprar')
                ->required()
                ->default(1),
            Select::make('size')
                ->label('Talla')
                ->options(self::sizeOptions())
                ->reactive()
                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                    $parts = $get('parts') ?? [];
                    foreach ($parts as $i => $part) {
                        $parts[$i]['size'] = $state;
                    }
                    $set('parts', $parts);
                }),
            Toggle::make('has_embroidery')->inline()
                ->label('Agregar bordado?')
                ->reactive(),
            TextInput::make('embroidery')
                ->label('Texto de Bordado')
                ->hidden(fn(Closure $get): bool => $get('has_embroidery') == false),
            Toggle::make('has_sublimate')->inline()
                ->label('Agregar sublimado?')
                ->reactive(),
            TextInput::make('sublimate')
                ->label('Texto de sublimado')
                ->hidden(fn(Closure $get): bool => $get('has_sublimate') == false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre'),

                TextColumn::make('sale_price')
                    ->money('gtq', true)
                    ->label("Precio"),

                TextColumn::make('quantity')->label("Cantidad"),

                TextColumn::make('colors')
                    ->label("Colores")
                    ->getStateUsing(function (Model $record): string {
                        $result = "";
                        if ($record->colors != null) {
                            foreach ($record->colors as $value) {
                                $result .= (ProductColor::find($value)->name ?? '') . ', ';
                            }
                        }
                        return $result;
                    })
                    ->wrap(),

                TextColumn::make("size")->label("Talla"),

                TextColumn::make('parts_summary')
                    ->label('Tallas Especiales')
                    ->getStateUsing(function (Model $record): string {
                        $orderProductId = $record->id ?? null;
                        if (!$orderProductId) return '';

                        $rows = OrderProductPart::query()
                            ->where('order_product_id', $orderProductId)
                            ->get();

                        return $rows
                            ->map(fn($p) => ($p->productPart->name ?? '') . ' (' . ($p->size ?? '-') . ')')
                            ->filter(fn($x) => trim($x) !== '()')
                            ->implode(', ');
                    })
                    ->wrap(),

                TextColumn::make('subtotal')
                    ->money('gtq', true)
                    ->label("SubTotal")
                    ->getStateUsing(fn(Model $record) => $record->quantity * $record->sale_price),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Agregar Producto')
                    ->slideOver()
                    ->modalWidth('4xl')
                    ->modalHeading('Agregar Producto')
                    ->modalButton('Guardar')
                    ->form(fn(AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if (!$state) {
                                    $set('parts', []);
                                    return;
                                }
                                $generalSize = $get('size');
                                $parts = ProductPart::query()
                                    ->where('product_id', $state)
                                    ->get(['id', 'name'])
                                    ->map(fn($p) => [
                                        'product_part_id' => $p->id,
                                        'part_name'       => $p->name,
                                        'size'            => $generalSize ?: null,
                                        'color_id'        => null,
                                    ])
                                    ->toArray();

                                $set('parts', $parts);
                            }),

                        TextInput::make('quantity')
                            ->label('Cantidad a comprar')
                            ->required()
                            ->default(1),

                        Select::make('size')
                            ->label('Talla')
                            ->options(self::sizeOptions())
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $parts = $get('parts') ?? [];
                                foreach ($parts as $i => $part) {
                                    if (empty($part['size'])) {
                                        $parts[$i]['size'] = $state;
                                    }
                                }

                                $set('parts', $parts);
                            }),

                        Repeater::make('parts')
                            ->label('Tallas del Producto')
                            ->itemLabel(fn(array $state): ?string => $state['part_name'] ?? 'Parte')
                            ->schema([
                                Hidden::make('product_part_id')->required(),

                                TextInput::make('part_name')
                                    ->label('Parte')
                                    ->disabled()
                                    ->dehydrated(false),

                                Select::make('size')
                                    ->label('Talla')
                                    ->options(self::sizeOptions())
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $parts = $get('parts') ?? [];
                                        foreach ($parts as $i => $part) {
                                            $parts[$i]['size'] = $state;
                                        }

                                        $set('parts', $parts);
                                    }),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->disableItemDeletion()
                            ->disableItemDeletion()
                            ->orderable(false)
                            ->hidden(fn(Closure $get): bool => $get('has_special_size') == false)
                            ->columnSpan('full'),

                        Select::make('colors')
                            ->multiple()
                            ->label('Color')
                            ->options(ProductColor::all()->pluck('name', 'id')),

                        Toggle::make('has_embroidery')->inline()
                            ->label('Agregar bordado?')
                            ->reactive(),

                        TextInput::make('embroidery')
                            ->label('Texto de Bordado')
                            ->hidden(fn(Closure $get): bool => $get('has_embroidery') == false),

                        Toggle::make('has_sublimate')->inline()
                            ->label('Agregar sublimado?')
                            ->reactive(),

                        TextInput::make('sublimate')
                            ->label('Texto de sublimado')
                            ->hidden(fn(Closure $get): bool => $get('has_sublimate') == false),

                        Toggle::make('has_special_size')->inline()
                            ->label('Agregar talla especial?')
                            ->reactive(),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['colors'] = json_encode($data['colors'] ?? []);
                        return $data;
                    })
                    ->preloadRecordSelect()
                    ->after(function (RelationManager $livewire, array $data) {
                        $orderId = $livewire->ownerRecord->id;
                        $productId = $data['recordId'] ?? null;

                        if ($productId) {
                            $orderProduct = OrderProduct::query()
                                ->where('order_id', $orderId)
                                ->where('product_id', $productId)
                                ->latest('id')
                                ->first();

                            if ($orderProduct) {
                                OrderProductPart::where('order_product_id', $orderProduct->id)->delete();

                                foreach (($data['parts'] ?? []) as $row) {
                                    if (empty($row['product_part_id'])) continue;

                                    OrderProductPart::create([
                                        'order_product_id' => $orderProduct->id,
                                        'product_part_id'  => $row['product_part_id'],
                                        'size'             => $row['size'] ?? null,
                                        'color_id'        => $row['color_id'] ?? null,
                                    ]);
                                }
                            }
                        }

                        self::$orderService->updateTotal($orderId);
                        self::$orderService->updateBalance($orderId);
                        $livewire->emit('refresh');
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make("goToProduct")
                        ->icon('heroicon-o-document')
                        ->label("Ver Producto Original")
                        ->action(fn(Model $record) => redirect()->intended('/admin/products/' . str($record->id))),

                    ViewAction::make()
                        ->label("Ver Producto")
                        ->slideOver()
                        ->form(fn(ViewAction $action): array => [
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
                            Toggle::make('has_special_size')->inline()
                                ->label('Agregar talla especial?')
                                ->reactive(),
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
                            Repeater::make('parts')
                                ->label('Tallas del Producto')
                                ->itemLabel(fn(array $state): ?string => $state['part_name'] ?? 'Parte')
                                ->afterStateHydrated(function (callable $set, callable $get, Model $record) {
                                    $orderProductId = $record->pivot->id ?? null;
                                    if (! $orderProductId) {
                                        $set('parts', []);
                                        return;
                                    }

                                    $generalSize = $get('size');

                                    $rows = \App\Models\OrderProductPart::query()
                                        ->where('order_product_id', $orderProductId)
                                        ->with(['productPart:id,name', 'color:id,name'])
                                        ->get()
                                        ->map(fn($r) => [
                                            'product_part_id' => $r->product_part_id,
                                            'part_name'       => $r->productPart?->name,
                                            'size'            => $r->size ?? $generalSize,
                                        ])
                                        ->toArray();

                                    $set('parts', $rows);
                                })
                                ->schema([
                                    Hidden::make('product_part_id'),

                                    TextInput::make('part_name')
                                        ->label('Parte')
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('size')
                                        ->label('Talla')
                                        ->disabled()
                                        ->dehydrated(false),
                                ])
                                ->columns(3)
                                ->disableItemDeletion()
                                ->disableItemDeletion()
                                ->orderable(false)
                                // ->deletable(false)
                                // ->hidden(fn (Closure $get): bool => $get('has_special_size') == false)
                                ->columnSpan('full'),

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
                                    fn(Closure $get): bool => $get('has_embroidery') == false
                                ),
                            Toggle::make('has_sublimate')->inline()
                                ->label('Agregar sublimado?')
                                ->reactive(),
                            TextInput::make('sublimate')
                                ->label('Texto de sublimado')
                                ->hidden(
                                    fn(Closure $get): bool => $get('has_sublimate') == false
                                ),
                        ]),

                    EditAction::make()
                        ->label('Editar Producto')
                        ->slideOver()
                        ->modalWidth('4xl')
                        ->modalHeading('Editar Producto')
                        ->modalButton('Guardar')
                        ->form(fn(EditAction $action): array => [
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
                                ->options(self::sizeOptions())
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $parts = $get('parts') ?? [];
                                    foreach ($parts as $i => $part) {
                                        $parts[$i]['size'] = $state;
                                    }

                                    $set('parts', $parts);
                                }),
                             Toggle::make('has_special_size')->inline()
                                ->label('Agregar talla especial?')
                                ->reactive(),

                            Repeater::make('parts')
                                ->label('Tallas del Producto')
                                ->itemLabel(fn(array $state): ?string => $state['part_name'] ?? 'Parte')
                                ->afterStateHydrated(function (callable $set, Model $record) {
                                    $orderProductId = $record->pivot->id ?? null;
                                    if (!$orderProductId) return;

                                    $rows = OrderProductPart::query()
                                        ->where('order_product_id', $orderProductId)
                                        ->with('productPart:id,name')
                                        ->get()
                                        ->map(fn($r) => [
                                            'product_part_id' => $r->product_part_id,
                                            'part_name'       => $r->productPart?->name,
                                            'size'            => $r->size
                                        ])
                                        ->toArray();

                                    $set('parts', $rows);
                                })
                                ->schema([
                                    Hidden::make('product_part_id')->required(),

                                    TextInput::make('part_name')
                                        ->label('Parte')
                                        ->disabled()
                                        ->dehydrated(false),

                                    Select::make('size')
                                        ->label('Talla')
                                        ->options(self::sizeOptions())
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                            $parts = $get('parts') ?? [];
                                            foreach ($parts as $i => $part) {
                                                $parts[$i]['size'] = $state;
                                            }

                                            $set('parts', $parts);
                                        }),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->orderable(false)
                                ->hidden(fn(Closure $get): bool => $get('has_special_size') == false)
                                ->disableItemDeletion()
                                ->disableItemCreation()
                                ->columnSpan('full'),

                            Select::make('colors')
                                ->multiple()
                                ->label('Color')
                                ->options(ProductColor::all()->pluck('name', 'id')),

                            Toggle::make('has_embroidery')->inline()
                                ->label('Agregar bordado?')
                                ->reactive(),

                            TextInput::make('embroidery')
                                ->label('Texto de Bordado')
                                ->hidden(fn(Closure $get): bool => $get('has_embroidery') == false),

                            Toggle::make('has_sublimate')->inline()
                                ->label('Agregar sublimado?')
                                ->reactive(),

                            TextInput::make('sublimate')
                                ->label('Texto de sublimado')
                                ->hidden(fn(Closure $get): bool => $get('has_sublimate') == false),

                           

                        ])
                        ->mutateFormDataUsing(function (array $data): array {
                            $data['colors'] = json_encode($data['colors'] ?? []);
                            return $data;
                        })
                        ->after(function (RelationManager $livewire, Model $record, array $data) {
                            $orderId = $livewire->ownerRecord->id;
                            $orderProductId = $record->pivot->id ?? null;

                            if ($orderProductId) {
                                OrderProductPart::where('order_product_id', $orderProductId)->delete();

                                foreach (($data['parts'] ?? []) as $row) {
                                    if (empty($row['product_part_id'])) continue;

                                    OrderProductPart::create([
                                        'order_product_id' => $orderProductId,
                                        'product_part_id'  => $row['product_part_id'],
                                        'size'             => $row['size'] ?? null,
                                        'color_id'        => $row['color_id'] ?? null,
                                    ]);
                                }
                            }

                            self::$orderService->updateTotal($orderId);
                            self::$orderService->updateBalance($orderId);
                            $livewire->emit('refresh');
                        }),

                    DetachAction::make()
                        ->label('Quitar')
                        ->modalHeading('Quitar de la orden')
                        ->modalSubheading('Esta accion es permanente, desea continuar con la eliminación?')
                        ->modalButton('Si, deseo quitarlo')
                        ->after(function (RelationManager $livewire, Model $record) {
                            $pivotId = $record->pivot->id ?? null;
                            if ($pivotId) {
                                OrderProductPart::where('order_product_id', $pivotId)->delete();
                            }

                            self::$orderService->updateTotal($livewire->ownerRecord->id);
                            self::$orderService->updateBalance($livewire->ownerRecord->id);
                            $livewire->emit('refresh');
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
