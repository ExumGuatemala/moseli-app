<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Tables;

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
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Enums\ProductEnum;
use App\Models\Feature;
use App\Models\Product;
use App\Models\OrderProduct;
use App\Services\OrderService;
use App\Services\ProductOrderService;
use App\Services\LogBookService;
use App\Repositories\ProductRepository;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected bool $allowsDuplicates = true;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?string $pluralModelLabel = 'Productos';

    protected static $orderService;
    protected static $orderProductService;
    protected static $logBookService;
    protected static $productRepository;

    public function __construct() {
        static::$orderService = new OrderService();
        static::$orderProductService = new ProductOrderService();
        static::$logBookService = new LogBookService();
        static::$productRepository = new ProductRepository();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Select::make('product_id')
                        ->label('Producto')
                        ->options(function (callable $get,callable $set) {
                            return Product::all()->pluck('name','id');
                        })
                        ->reactive(),
                    TextInput::make('quantity')
                        ->label('Cantidad a comprar')
                        ->required(),
                    Select::make('size')
                        ->label('Talla')
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
                        ])
                        ->reactive()
                        ->afterStateUpdated(
                            function ($state, callable $set, callable $get)
                                {
                                    $product_types = Feature::where('product_types_id',Product::find($get('product_id'))->first()->type->id)->get();
                                    foreach ($product_types as $value) {
                                        $value["sizes"] = $value->sizis->where('name', $state)->first()->length ?? "no hay tamaño registrado para esta talla por defecto";
                                    }
                                    $set("features",$product_types->toArray());
                                    $set('total', Product::find($get('product_id'))->first()->type->size_prices->where('name', $state)->first()->price ?? "no hay precio para esta talla establecido por defecto");
                                }
                        ),
                    Repeater::make('features')
                        ->label("Caracteristicas")
                        ->schema([
                                TextInput::make('sizes')
                                    ->label("Tamaño")
                                    ->reactive()
                                    ->afterStateUpdated(
                                        function ($state, callable $set, callable $get, RelationManager $livewire)
                                            {
                                                $livewire->mountedTableActionData['total'] = self::$orderProductService->getThePriceOfAProduct($livewire->mountedTableActionData['features'], Product::find($livewire->mountedTableActionData["product_id"])->type->size_prices);
                                            }
                                    )
                                    ,
                        ])
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                        ->createItemButtonLabel('Añadir una talla')
                        ->columns(2)
                        ->columnSpan('full')
                        ->defaultItems(0)
                        ->collapsed(),
                   TextInput::make('total')->label("Total"),
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
                        return $record->quantity * $record->total;
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make('add')
                    ->label('Agregar Producto')
                    ->size('md')
                    ->modalWidth('4xl')
                    ->modalHeading('Agregar Producto')
                    ->modalButton('Guardar')
                            ->form(fn (): array => [
                                Select::make('product_id')
                                    ->label('Producto')
                                    ->options(function (callable $get,callable $set) {
                                        return Product::all()->pluck('name','id');
                                    })
                                    ->reactive(),
                                TextInput::make('quantity')
                                    ->label('Cantidad a comprar')
                                    ->required(),
                                Select::make('size')
                                    ->label('Talla')
                                    ->options(ProductEnum::SIZES)
                                    ->reactive()
                                    ->afterStateUpdated(
                                        function ($state, callable $set, callable $get)
                                            {
                                                $product_types = Feature::where('product_types_id',Product::find($get('product_id'))->first()->type->id)->get();
                                                foreach ($product_types as $value) {
                                                    $value["sizes"] = $value->sizis->where('name', $state)->first()->length ?? "no hay tamaño registrado para esta talla por defecto";
                                                }

                                                $set("features",$product_types->toArray());
                                                $set('total', Product::find($get('product_id'))->first()->type->size_prices->where('name', $state)->first()->price ?? "no hay precio para esta talla establecido por defecto");
                                            }
                                    ),

                                Repeater::make('features')
                                    ->label("Caracteristicas")
                                    ->schema([
                                            TextInput::make('sizes')
                                                ->label("Tamaño")
                                                ->reactive()
                                                ->afterStateUpdated(
                                                    function ($state, callable $set, callable $get, RelationManager $livewire)
                                                        {
                                                            $livewire->mountedTableActionData['total'] = self::$orderProductService->getThePriceOfAProduct($livewire->mountedTableActionData['features'], Product::find($livewire->mountedTableActionData["product_id"])->type->size_prices);
                                                        }
                                                )
                                                ,
                                    ])
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                    ->createItemButtonLabel('Añadir una talla')
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->collapsed(),
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
                                TextInput::make('total')->label("Total")
                        ])
                        ->action(
                            function (array $data, RelationManager $livewire) {
                                $newOrderProduct = new OrderProduct();
                                $newOrderProduct->order_id = $livewire->ownerRecord->id;
                                $newOrderProduct->product_id = $data['product_id'];
                                $newOrderProduct->quantity = $data['quantity'];
                                $newOrderProduct->total = $data['total'];
                                $newOrderProduct->features = $data['features'];
                                $newOrderProduct->size = $data['size'];
                                $newOrderProduct->save();
                                self::$orderService->updateTotal($livewire->ownerRecord->id);
                                self::$orderService->updateBalance($livewire->ownerRecord->id);
                                self::$logBookService->saveEvent($livewire->ownerRecord->id, "App\Models\Order",Auth::user()->id, "Se agregó el producto = ".self::$productRepository->getOne($livewire->mountedTableActionData["product_id"])." en la orden con el codigo = ".$livewire->ownerRecord->key);
                                $livewire->emit('refresh');
                            }
                        ),
                ])
            ->actions([
                EditAction::make()
                    ->after(function (RelationManager $livewire) {
                        self::$orderService->updateTotal($livewire->ownerRecord->id);
                        self::$orderService->updateBalance($livewire->ownerRecord->id);
                        self::$logBookService->saveEvent($livewire->ownerRecord->id, "App\Models\Order",Auth::user()->id, "Se editó el producto = ".$livewire->mountedTableActionData["name"]." en la orden con el codigo = ".$livewire->ownerRecord->key);
                        $livewire->emit('refresh');
                    }),
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make("goToProduct")
                    ->label("Ver Producto")
                    ->action(function (Model $record) {
                        redirect()->intended('/admin/products/'.str($record->id));
                    }),
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
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['colors'] = json_encode($data['colors']);
                        return $data;
                    })
                    ->after(function (RelationManager $livewire) {
                        self::$orderService->updateTotal($livewire->ownerRecord->id);
                        self::$orderService->updateBalance($livewire->ownerRecord->id); 
                        self::$logBookService->saveEvent($livewire->ownerRecord->id, "App\Models\Order",Auth::user()->id, "Se editó el producto = ".$livewire->mountedTableActionData["name"]." en la orden con el codigo = ".$livewire->ownerRecord->key);                                         
                        $livewire->emit('refresh');
                    }),
                DetachAction::make()
                    ->label('Quitar')
                    ->modalHeading('Quitar de la orden')
                    ->modalSubheading('Esta accion es permanente, desea continuar con la eliminación?')
                    ->modalButton('Si, deseo quitarlo')
                    ->before(function (RelationManager $livewire, Model $record) {
                        self::$logBookService->saveEvent($livewire->ownerRecord->id, "App\Models\Order",Auth::user()->id, "Se eliminó el producto = ".$record->name." en la orden con el codigo = ".$livewire->ownerRecord->key);
                    })
                    ->after(function (RelationManager $livewire) {
                        self::$orderService->updateTotal($livewire->ownerRecord->id);
                        self::$orderService->updateBalance($livewire->ownerRecord->id);
                        $livewire->emit('refresh');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DissociateBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
