<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use App\Models\Client;
use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Closure;

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
                    ->maxLength(255)
                    ->label("Nombre Completo")
                    ->columnSpan('full'),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->label("Correo Electrónico"),
                TextInput::make('key')
                    ->maxLength(255)
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
                    ->maxLength(255)
                    ->label("Teléfono 1"),
                TextInput::make('phone2')
                    ->tel()
                    ->maxLength(255)
                    ->label("Teléfono 2"),
                TextInput::make('address')
                    ->required()
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
                Tables\Actions\ViewAction::make()
                    ->label("Ver")
                    ->modalHeading('Detalles De Cliente'),
                Tables\Actions\EditAction::make()
                    ->label("Editar")
                    ->modalHeading('Editar Cliente'),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->modalHeading('Eliminar Cliente')
                    ->modalSubheading('Esta accion es permanente, desea continuar con la eliminación?')
                    ->modalButton('Si, deseo eliminarlo'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }    
}
