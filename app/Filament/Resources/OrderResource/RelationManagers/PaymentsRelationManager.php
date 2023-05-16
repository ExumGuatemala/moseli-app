<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Services\OrderService;
class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'amount';
    protected static ?string $navigationLabel = 'Pagos';

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
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function (RelationManager $livewire, array $data) {
                    $data['order_id'] = $livewire->ownerRecord->id;
                    return $data;
                })
                ->after(function (RelationManager $livewire) {
                        OrderService::updateBalance($livewire->ownerRecord->id);
                        $livewire->emit('refresh');
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->after(function (RelationManager $livewire) {
                        OrderService::updateBalance($livewire->ownerRecord->id);
                        $livewire->emit('refresh');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
