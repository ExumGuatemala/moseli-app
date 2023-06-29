<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables;
use App\Services\OrderService;
use App\Services\LogBookService;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Pagos';
    protected static ?string $pluralModelLabel = 'Productos';

    protected static $orderService;
    protected static $logBookService;
    protected static $productRepository;

    public function __construct() {
        static::$orderService = new OrderService();
        static::$logBookService = new LogBookService();
        static::$productRepository = new ProductRepository();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
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
                TextColumn::make('subtotal')
                    ->money('gtq', true)
                    ->label("SubTotal")
                    ->getStateUsing(function (Model $record) {
                        return $record->quantity * $record->sale_price;
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                ->label('Agregar Producto')
                ->modalHeading('Agregar Producto')
                ->modalButton('Guardar')
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('quantity')
                            ->label('Cantidad a comprar')
                            ->required()
                            ->default(1),
                    ])
                    ->preloadRecordSelect()
                    ->after(function (RelationManager $livewire) {
                        self::$orderService->updateTotal($livewire->ownerRecord->id);
                        self::$orderService->updateBalance($livewire->ownerRecord->id);
                        self::$logBookService->saveEvent($livewire->ownerRecord->id, "App\Models\Order",Auth::user()->id, "Se agregó el producto = ".self::$productRepository->getOne($livewire->mountedTableActionData["recordId"])." en la orden con el codigo = ".$livewire->ownerRecord->key);                                         
                        $livewire->emit('refresh');
                    }),
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
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
