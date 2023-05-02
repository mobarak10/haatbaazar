<?php

namespace App\Http\Controllers;

trait PermissionForPropertyValidation
{
    /**
     * Has permission for access controller method
     *
     * @param string $permissionType
     */
    protected function hasPermission(string $permissionType)
    {
        // get permission name
        $permission_name = $this->getPermissionName($permissionType);

        if (!auth()->user()->can($permission_name)) abort(401);
    }

    /**
     * Get Permission name
     *
     * @param string $permissionType
     * @return string
     */
    protected function getPermissionName(string $permissionType): string
    {
//        if (!property_exists($this, 'permission_for')) {
//            throw new ($this->generateErrorMessage());
//        }

        return str_contains($permissionType, '-')
            ?
            $this->permission_for . '_' . $permissionType
            :
            $this->permission_for . '-' . $permissionType;
    }

    /**
     * Generate property not found message
     *
     * @return string
     */
    private function generateErrorMessage(): string
    {
        return 'permission_for property is not declared on';
    }
}
