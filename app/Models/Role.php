<?php

namespace App\Models;

class Role extends \Spatie\Permission\Models\Role
{
    public function getAllRoles()
    {
        return $this->select('id', 'name')->orderBy('name', 'ASC')->where('name', '!=', 'super_admin')->get();
    }

    public function addRole($role, $permissions)
    {
        return $this->create($role)->syncPermissions($permissions);
    }
}
