<?php

namespace App\Services\v1\Impl;

use App\Models\User;
use App\Services\v1\Service\UserService;
use Illuminate\Support\Facades\Hash;

class UserServiceImpl implements UserService
{

    protected $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function getAllUsers($with = false)
    {
        return $this->user->getAllUsers($with);
    }

    public function addUser($user)
    {
        $newUser = $this->user->addUser([
            'name'      => $user['name'],
            'email'     => $user['email'],
            'password'  => Hash::make($user['password']),
        ]);

        $newUser->syncRoles($user['roles']);

        return $newUser;
    }

    public function getUserById($id)
    {
        return $this->user->getUserById($id);
    }

    public function updateUser($id, $newUser)
    {
        $user = $this->getUserById($id);

        $this->user->updateUser($user, $newUser);

        $user->syncRoles($newUser['roles']);

        return $user;
    }

    public function deleteUser($id)
    {
        $this->getUserById($id);

        return $this->user->deleteUser($id);
    }
}
