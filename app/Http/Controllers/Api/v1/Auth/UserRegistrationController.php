<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\RegisterRequest;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRegistrationController extends Controller
{

    public function register(RegisterRequest $request)
    {

        try
        {
            $user = User::create([
                'name'     => $request['name'],
                'email'    => $request['email'],
                'password' => Hash::make($request['password'])
            ]);

            // assign a role (default one)
            $user_role = Role::where(['name' => config('roles.default_user_role')])->first();

            if ($user_role)
            {
                $user->assignRole($user_role);
            }

            return new UserResource($user);

        }catch(\Exception $e)
        {
            return sendError('Something went wrong', [], 500);
        }
    }
}
