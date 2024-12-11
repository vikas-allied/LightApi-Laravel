<?php

namespace App\Services\v1\Service;

interface RoleService
{
    public function getAllRoles();

    public function addRole($roleData);

    public function getRoleByRoleId($roleId);

    public function updateRole($roleId, $newRole);

    public function deleteRole($roleId);

}
