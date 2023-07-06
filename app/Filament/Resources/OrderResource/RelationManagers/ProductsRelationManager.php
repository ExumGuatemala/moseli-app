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
use Filament\Forms\Components\Select;
use App\Models\ProductColor;
use App\Models\Product;
use Filament\Forms\Components\Toggle;
use Closure;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\Action;
use Filament\Tables;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Grid;
class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

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
            ->schema(
                Grid::make(1)
                ->schema([
                    Select::make('product_id')
                    ->label('Producto')
                    ->options(
                        Product::get()->pluck('name','id')
                    ),
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
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->getStateUsing(function (Model $record): String {
                        return $record->product->name;
                    }),
                TextColumn::make('sale_price')
                    ->money('gtq', true)
                    ->label("Precio")
                    ->getStateUsing(function (Model $record): String {
                        return $record->product->sale_price;
                    }),
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
                TextColumn::make('embroidery')
                    ->label("Bordado")
                    ->wrap(),
                TextColumn::make('sublimate')
                    ->label("Sublimado")
                    ->wrap(),
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
                Tables\Actions\CreateAction::make()
                ->label('Agregar Producto')
                ->slideOver()
                ->modalWidth('4xl')
                ->modalHeading('Agregar Producto')
                ->modalButton('Guardar')
                ->mutateFormDataUsing(function (RelationManager $livewire, array $data) {
                    $data['order_id'] = $livewire->ownerRecord->id;
                    return $data;
                })
                ->after(function (RelationManager $livewire) {  
                    self::$orderService->updateTotal($livewire->ownerRecord->id);
                    self::$orderService->updateBalance($livewire->ownerRecord->id);                        
                    $livewire->emit('refresh');
                }),
            ])
            ->actions([
                Action::make("goToProduct")
                    ->label("Ver Producto")
                    ->action(function (Model $record) {
                        redirect()->intended('/admin/products/'.str($record->product->id));
                    }),
                EditAction::make()
                    ->label('Editar Producto')
                    ->slideOver()
                    ->modalWidth('4xl')
                    ->modalHeading('Editar Producto')
                    ->modalButton('Guardar')
                    ->form(fn (EditAction $action): array => [
                        Select::make('product_id')
                            ->label('Producto')
                            ->options(
                                Product::get()->pluck('name','id')
                            ),
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
                    ->after(function (RelationManager $livewire) {
                        self::$orderService->updateTotal($livewire->ownerRecord->id);
                        self::$orderService->updateBalance($livewire->ownerRecord->id); 
                        $livewire->emit('refresh');
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('Quitar')
                    ->modalHeading('Quitar de la orden')
                    ->modalSubheading('Esta accion es permanente, desea continuar con la eliminación?')
                    ->modalButton('Si, deseo quitarlo')
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
