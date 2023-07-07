<?php

namespace App\Repositories;

use App\Models\ModelHasRole;

class ModelHasRoleRepository
{
    public function save($user_id, $model_type, $role_id){
        $properties = array('role_id' => $user_id, 'model_type' => $model_type, 'model_id' => $role_id);
        $roleuser = new ModelHasRole($properties);
        $roleuser->save();
    }

    public function getRoleByUserId($user_id) {
        return ModelHasRole::where('model_id', $user_id)->first();
    }
}