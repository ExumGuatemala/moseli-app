<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use App\Models\Client;
use App\Models\Departamento;
use App\Models\Municipio;
use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;


    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clientes';
    protected static ?string $navigationLabel = 'Clientes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label("Nombre Completo")
                    ->columnSpan('full'),
                TextInput::make('email')
                    ->email()
                    ->label("Correo Electrónico"),
                TextInput::make('key')
                    ->label("Código")
                    ->disabled()
                    ->afterStateHydrated(function (TextInput $component, $state) {
                        if(!$state){
                            $component->state(strtoupper(substr(bin2hex(random_bytes(ceil(8 / 2))), 0, 8)));
                        }
                    }),
                TextInput::make('phone1')
                    ->tel()
                    ->required()
                    ->label("Teléfono 1"),
                TextInput::make('phone2')
                    ->tel()
                    ->label("Teléfono 2"),
                TextInput::make('address')
                    ->columnSpan('full')
                    ->label("Dirección"),
                Select::make('departamentoId')
                    ->label('Departamento')
                    ->afterStateHydrated(function (Model|null $record, Select $component) {
                        $municipio = $record == null ? $record : Municipio::find($record->municipio_id);
                        if(!$municipio){
                            $component->state(13);
                        } else {
                            $component->state($municipio->departamento->id);
                        }
                    })
                    ->options(Departamento::all()->pluck('name','id')->toArray())
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('municipioId', null)),

                Select::make('municipioId')
                    ->label('Municipio')
                    ->relationship('municipio', 'name')
                    ->options(function (callable $get) {
                        $departamento = Departamento::find($get('departamentoId'));

                        if(!$departamento){
                            return Municipio::all()->pluck('name','id');
                        }

                        return $departamento->municipios->pluck('name','id');

                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label("Nombre Completo")
                    ->searchable(['name']),
                TextColumn::make('key')
                    ->label("Código"),
                TextColumn::make('phone1')
                    ->label("Teléfono 1"),
                TextColumn::make('email')
                    ->label("Correo Electrónico"),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label("Fecha de Creación"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageClients::route('/'),
        ];
    }
}
