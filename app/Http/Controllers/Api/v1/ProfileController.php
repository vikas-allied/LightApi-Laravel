<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function updateProfile(Request $request, string $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:255',
                'email' => 'required|email|max:255|unique:users,email,'. $id . ',id',
                'password' => 'nullable|string|min:6|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).+$/',
                'confirm_password' => 'nullable|string|same:password|min:6|max:255',
            ]);

            if ($validator->fails()) {

                $error = trans('errors.validation_error');

                return sendError($error['message'], $validator->errors()->toArray(), $error['status_code']);
            }

            $data = $validator->validated();

            if ($data['password'] == null) {
                unset($data['password'], $data['confirm_password']);
            }

            $user = User::findOrFail($id);

            $user->update($data);

            return sendResponse('Profile is updated successfully', $user->toArray());

        }

        catch(ModelNotFoundException $modelNotFoundException) {

            Log::info($modelNotFoundException->getMessage());

            $error = trans('errors.user_not_found');

            return sendError($error['message'], [], $error['status_code']);
        }

        catch (\Exception $e)
        {
            Log::info($e->getMessage());

            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }
    }
}
