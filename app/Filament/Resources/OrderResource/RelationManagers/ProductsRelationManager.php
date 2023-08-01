<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\AttachAction;
use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables;
use App\Models\Feature;
use App\Models\Product;
use App\Models\OrderProduct;
use App\Services\OrderService;
use App\Services\ProductOrderService;
use App\Services\LogBookService;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Pagos';
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
                    TextInput::make('total')->label("total")

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
                TextColumn::make('total')
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
                                    ->defaultItems(0)
                                    ->collapsed(),
                                TextInput::make('total')->label("total")
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
