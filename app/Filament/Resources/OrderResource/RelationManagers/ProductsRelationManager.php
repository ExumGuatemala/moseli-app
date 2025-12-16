<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

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
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan('full')
                    ->label("Nombre"),
                TextInput::make('sale_price')
                    ->required()
                    ->mask(fn (TextInput\Mask $mask) => $mask->money(prefix: 'Q.', thousandsSeparator: ',', decimalPlaces: 2))
                    ->label("Precio de Venta"),
                TextInput::make('existence')
                    ->numeric()
                    ->label("Existencia")
                    ->afterStateHydrated(function (TextInput $component, $state) {
                        if(!$state){
                            $component->state(1);
                        }
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
                // Select::make('colors')
                //     ->multiple()
                //     ->label('Color')
                //     ->options(ProductColor::all()->pluck('name', 'id')),
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
                        return $record->quantity * $record->sale_price;
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                ->label('Agregar Producto')
                ->slideOver()
                ->modalWidth('4xl')
                ->modalHeading('Agregar Producto')
                ->modalButton('Guardar')
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
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
                        $data['colors'] = json_encode($data['colors']);
                        return $data;
                    })
                    ->preloadRecordSelect()
                    ->after(function (RelationManager $livewire) {  
                        self::$orderService->updateTotal($livewire->ownerRecord->id);
                        self::$orderService->updateBalance($livewire->ownerRecord->id);                        
                        $livewire->emit('refresh');
                    }),
                Tables\Actions\CreateAction::make(),
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
                            $data['colors'] = json_encode($data['colors']);
                            return $data;
                        })
                        ->after(function (RelationManager $livewire) {
                            self::$orderService->updateTotal($livewire->ownerRecord->id);
                            self::$orderService->updateBalance($livewire->ownerRecord->id); 
                            $livewire->emit('refresh');
                        }),
                    DetachAction::make()
                        ->label('Quitar')
                        ->modalHeading('Quitar de la orden')
                        ->modalSubheading('Esta accion es permanente, desea continuar con la eliminación?')
                        ->modalButton('Si, deseo quitarlo')
                        ->after(function (RelationManager $livewire) {
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
