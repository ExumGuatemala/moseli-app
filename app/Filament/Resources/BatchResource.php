<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BatchResource\Pages;
use App\Filament\Resources\BatchResource\RelationManagers;
use App\Models\Batch;
use App\Models\OrderState;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BatchResource extends Resource
{
    protected static ?string $model = Batch::class;

    

    protected static ?string $navigationGroup = 'Producción';
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $modelLabel = 'Lote';
    protected static ?string $pluralModelLabel = 'Lotes';
    protected static ?string $navigationLabel = 'Lotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->label("Código de Lote")
                    ->disabled()
                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                        if(!$state){
                            $component->state(strtoupper(substr(bin2hex(random_bytes(ceil(8 / 2))), 0, 8)));
                        }
                }),
                Forms\Components\Select::make('state_id')
                    ->label('Estado de Lote')
                    ->afterStateHydrated(function (Model|null $record, Forms\Components\Select $component) {
                        $order = $record == null ? $record : Batch::find($record->id);
                        if(!$order){
                            $orderStateIdForRecibida = OrderState::where('process_order', 1)->first()->id;
                            $component->state($orderStateIdForRecibida);
                        } else {
                            $component->state($order->state_id);
                        }
                    })
                    ->options(OrderState::all()->pluck('name', 'id'))
                    ->relationship('state', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Fecha de Inicio')
                    ->required()
                    ->default(now()),
                Forms\Components\DatePicker::make('end')
                    ->label('Fecha de Fin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Código de Lote')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('state_id')
                    ->label('Estado de Lote')
                    ->getStateUsing(function (Model $record) {
                        return $record->state->name;
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->label('Fecha de Inicio'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->label('Fecha de Creación')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('state_id')
                    ->label('Estado de Lote')
                    ->multiple()
                    ->options(
                        OrderState::get()->pluck('name', 'id')
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBatches::route('/'),
            'create' => Pages\CreateBatch::route('/create'),
            'view' => Pages\ViewBatch::route('/{record}'),
            'edit' => Pages\EditBatch::route('/{record}/edit'),
        ];
    }    
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
