<?php

namespace App\Services\Impl;

use App\Models\Role;
use App\Services\Service\RoleService;

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

    public function addRole($role, $permissions)
    {
        return $this->role->addRole($role, $permissions);
    }
}
