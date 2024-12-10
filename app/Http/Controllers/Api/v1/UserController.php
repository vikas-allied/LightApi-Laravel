<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use App\Services\Service\RoleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;

class UserController extends Controller implements HasMiddleware
{

    protected function __construct(RoleService $roleService)
    {
    }


    public static function middleware()
    {
        return [

            new Middleware('permission:user_list', only: ['index']),
            new Middleware('permission:view_user', only: ['show']),
            new Middleware('permission:update_user', only: ['edit', 'update']),
            new Middleware('permission:create_user', only: ['create', 'store']),
            new Middleware('permission:delete_user', only: ['destroy']),
        ];
    }


    /**
     * Display a listing of the user resource.
     */
    public function index()
    {
        try {


            $users = User::with('roles:name')->select('id', 'name', 'email')->where('name', '!=', 'Super Admin User')->get();

            //return response()->json([ 'data' => $users]);

            return sendResponse('User List', $users->toArray());

        }catch (\Exception $e)
        {
            \Log::info($e->getMessage());

            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {

            $roles = Role::select('id', 'name')->orderBy('name', 'ASC')->get();

            $data = [
                'roles' => $roles,
            ];

            return sendResponse('Create User Form', $data);

        }catch (\Exception $e) {

            \Log::info($e->getMessage());

            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:6|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).+$/',
                'confirm_password' => 'required|string|same:password|min:6|max:255',
                'roles' => 'required|array|min:1',  // Ensure that roles are provided
                'roles.*' => 'exists:roles,name',   // Ensure each role exists in the roles table
            ]);

            if ($validator->fails()) {

                $error = trans('errors.validation_error');

                return sendError($error['message'], $validator->errors()->toArray(), $error['status_code']);
            }

            $data = $validator->validated();

            $user = User::create([
               'name'      => $data['name'],
               'email'     => $data['email'],
               'password'  => Hash::make($data['password']),
            ]);

            $user->syncRoles($data['roles']);

            return sendResponse('User is created successfully', $user->toArray());

        }catch (\Exception $e)
        {
            \Log::info($e->getMessage());

            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $user = User::findOrFail($id);

            $hasRoles = $user->roles()->pluck('name');

            $data = [
                'user'      => $user,
                'hasRoles'  => $hasRoles
            ];

            return sendResponse('User Data', $data);

        }

        catch(ModelNotFoundException $modelNotFoundException) {

            \Log::info($modelNotFoundException->getMessage());

            $error = trans('errors.user_not_found');

            return sendError($error['message'], [], $error['status_code']);
        }


        catch (\Exception $e) {

            \Log::info($e->getMessage());

            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {

                $user = User::findOrFail($id);

                $roles = Role::select('id', 'name')->orderBy('name', 'ASC')->get();

                $hasRoles = $user->roles()->pluck('name');

                $data = [
                    'user'      => $user,
                    'roles'     => $roles,
                    'hasRoles'  => $hasRoles
                ];

                return sendResponse('Edit User Form', $data);

            }

            catch(ModelNotFoundException $modelNotFoundException) {

                \Log::info($modelNotFoundException->getMessage());

                $error = trans('errors.user_not_found');

                return sendError($error['message'], [], $error['status_code']);
            }


            catch (\Exception $e) {

                \Log::info($e->getMessage());

                $error = trans('errors.server_error');

                return sendError($error['message'], [], $error['status_code']);
            }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|min:2|max:255',
                'email' => 'nullable|email|max:255|unique:users,email,'. $id . ',id',
                'password' => 'nullable|string|min:6|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).+$/',
                'confirm_password' => 'nullable|string|same:password|min:6|max:255',
                'roles' => 'required|array|min:1',  // Ensure that roles are provided
                'roles.*' => 'exists:roles,name',   // Ensure each role exists in the roles table
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

            $user->syncRoles($data['roles']);

            return sendResponse('User is updated successfully', $user->toArray());

        }

        catch(ModelNotFoundException $modelNotFoundException) {

            \Log::info($modelNotFoundException->getMessage());

            $error = trans('errors.user_not_found');

            return sendError($error['message'], [], $error['status_code']);
        }

        catch (\Exception $e)
        {
            \Log::info($e->getMessage());

            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            $user = User::findOrFail($id);

            $user->delete();

            return sendResponse('User is deleted successfully');

        }

        catch(ModelNotFoundException $modelNotFoundException) {

            \Log::info($modelNotFoundException->getMessage());

            $error = trans('errors.user_not_found');

            return sendError($error['message'], [], $error['status_code']);
        }

        catch (\Exception $e)
        {
            \Log::info($e->getMessage());

            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }

    }


}
