<?php

namespace App\Models;

class Role extends \Spatie\Permission\Models\Role
{
    public function getAllRoles()
    {
        return $this->select('id', 'name')->orderBy('name', 'ASC')->where('name', '!=', 'super_admin')->get();
    }

    public function addRole($role)
    {
        return $this->create($role);
    }

    public function getRoleByRoleId($roleId)
    {
        return $this->findOrFail($roleId);
    }

    public function updateRole($role, $newRole)
    {
        return $role->update($newRole);
    }

    public function deleteRole($roleId)
    {
        return $this->where('id', $roleId)->delete();
    }

}
