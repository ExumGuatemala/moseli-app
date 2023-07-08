<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\ModelHasRole;
use App\Services\ModelHasRoleService;

use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    protected static $modelRoleService;
    public function __construct() {
        static::$modelRoleService = new ModelHasRoleService();
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $password = $data['password'];
        $data['password'] = Hash::make($password);
        return $data;
    }
}
