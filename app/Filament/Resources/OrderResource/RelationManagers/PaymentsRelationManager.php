<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Support\RelationManagerActivity;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Services\OrderService;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'amount';
    protected static ?string $navigationLabel = 'Pagos';
    protected static ?string $pluralModelLabel = 'Pagos';

    protected static $orderService;

    public function __construct() {
        static::$orderService = new OrderService();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('amount')
                    ->label('Monto')
                    ->required()
                    ->mask(fn (TextInput\Mask $mask) => $mask->money(prefix: 'Q.', thousandsSeparator: ',', decimalPlaces: 2)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        
            ->columns([
                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('gtq', true),
                TextColumn::make('created_at')
                    ->label('Fecha de Pago')
                    ->dateTime(),

            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->label('Nuevo Pago')
                ->modalHeading('Crear Nuevo Pago')
                ->modalButton('Guardar')
                ->mutateFormDataUsing(function (RelationManager $livewire, array $data) {
                    $data['order_id'] = $livewire->ownerRecord->id;
                    return $data;
                })
                ->after(function (RelationManager $livewire, ?Model $record) {
                    self::$orderService->updateBalance($livewire->ownerRecord->id); 
                    if ($record) {
                        RelationManagerActivity::log('Creado', $livewire->ownerRecord, 'payments', $record, [
                            'amount' => $record->amount,
                        ]);
                    }
                    $livewire->emit('refresh');
                }),
            ])
            ->actions([

            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->after(function (RelationManager $livewire, ?Collection $records) {
                        foreach ($records ?? collect() as $record) {
                            RelationManagerActivity::log('Eliminado', $livewire->ownerRecord, 'payments', $record, [
                                'amount' => $record->amount,
                            ]);
                        }

                        self::$orderService->updateBalance($livewire->ownerRecord->id);
                        $livewire->emit('refresh');
                    }),
                Tables\Actions\RestoreBulkAction::make()
                    ->after(function (RelationManager $livewire, ?Collection $records) {
                        foreach ($records ?? collect() as $record) {
                            RelationManagerActivity::log('Restaurado', $livewire->ownerRecord, 'payments', $record, [
                                'amount' => $record->amount,
                            ]);
                        }

                        self::$orderService->updateBalance($livewire->ownerRecord->id);
                        $livewire->emit('refresh');
                    }),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
