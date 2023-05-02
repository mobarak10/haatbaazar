<?php

namespace App\Services\PermissionService;
use App\Models\User;

class PermissionService extends BasePermissionService
{
    public function givePermissionToUser(User $user, string $permissionName)
    {
        if ($this->checkPermissionExists($permissionName)){
            $user->givePermissionTo($permissionName);
        }else{
            $this->generateAllPermissions();
            $user->givePermissionTo($permissionName);
        }
    }
}
