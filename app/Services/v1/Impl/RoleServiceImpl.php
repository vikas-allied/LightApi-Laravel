<?php

namespace App\Services\v1\Impl;

use App\Models\Role;
use App\Services\v1\Service\RoleService;

class RoleServiceImpl implements RoleService
{

    protected $role;

    public function __construct()
    {
        $this->role = new Role();
    }

    public function getAllRoles()
    {
        return $this->role->getAllRoles();
    }

    public function addRole($roleData)
    {

        $role = $this->role->addRole([
            'name' => $roleData['name'],
            'guard_name' => 'web'
        ]);

        $role->syncPermissions($roleData['permissions']);

        return $role;
    }

    public function getRoleByRoleId($roleId)
    {
        return $this->role->getRoleByRoleId($roleId);
    }


    public function updateRole($roleId, $newRole)
    {
        $role = $this->getRoleByRoleId($roleId);

        $this->role->updateRole($role, $newRole);

        $role->syncPermissions($newRole['permissions']);

        return $role;
    }

    public function deleteRole($roleId)
    {
        $role = $this->getRoleByRoleId($roleId);

        $count = $role->users()->count();

        if ($count > 0)
        {
            return 2; // For Not TO Do Anything
        }

        $result = $this->role->deleteRole($roleId);

        if ($result)
        {
            return 1;
        }

        return 0;
    }
}
