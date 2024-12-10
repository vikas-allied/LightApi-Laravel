<?php

namespace App\Services\Service;

interface RoleService
{
    public function getAllRoles();

    public function addRole($role, $permissions);

}
