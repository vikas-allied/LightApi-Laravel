<?php

namespace App\Services\v1\Service;

interface UserService
{
    public function getAllUsers($with);

    public function addUser($user);

    public function getUserById($id);

    public function updateUser($id, $newUser);

    public function deleteUser($id);
}
