<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\LoginRequest;
use App\Http\Resources\v1\UserResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Enums\TokenAbility;

class UserLoginController extends Controller
{
    public function login(LoginRequest $request)
    {

        try
        {

            if (!Auth::attempt($request->only('email', 'password')))
            {
                $error = trans('errors.invalid_credentials');

                return sendError($error['message'], [], $error['status_code']);
            }

            return new UserResource(\auth()->user());

        }
        catch(\Exception $e)
        {

            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }

    }


    public function logout(Request $request)
    {
        try
        {
            // Get the currently authenticated user
            $user = $request->user();

            if ($user)
            {
                // Delete the current access token
                $user->currentAccessToken()->delete();
            }

            return sendResponse('Logout Successful');

        }
        catch (\Exception $e)
        {
            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }
    }


    public function refreshToken(Request $request)
    {
        $accessToken = $request->user()->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));

        return response(['message' => "Token généré", 'token' => $accessToken->plainTextToken]);
    }



}
