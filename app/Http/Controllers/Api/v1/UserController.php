<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\v1\Service\RoleService;
use App\Services\v1\Service\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller implements HasMiddleware
{

    protected $roleService, $userService;

    public function __construct(RoleService $roleService, UserService $userService)
    {
        $this->roleService = $roleService;
        $this->userService = $userService;
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
        try
        {
            $users = $this->userService->getAllUsers();

            return sendResponse('User List', $users->toArray());

        }
        catch (\Exception $e)
        {
            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try
        {
            $roles = $this->roleService->getAllRoles();

            return sendResponse('Create User Form', ['roles' => $roles]);

        }
        catch (\Exception $e)
        {
            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:6|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).+$/',
                'confirm_password' => 'required|string|same:password|min:6|max:255',
                'roles' => 'required|array|min:1',  // Ensure that roles are provided
                'roles.*' => 'exists:roles,name',   // Ensure each role exists in the roles table
            ]);

            if ($validator->fails())
            {
                $error = trans('errors.validation_error');

                return sendError($error['message'], $validator->errors()->toArray(), $error['status_code']);
            }

            $data = $validator->validated();

            $user = $this->userService->addUser($data);

            if ($user)
            {
                return sendResponse('User is created successfully', $user->toArray());
            }

        }
        catch (\Exception $e)
        {
            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try
        {
            $user = $this->userService->getUserById($id);

            $hasRoles = $user->roles()->pluck('name');

            return sendResponse('User Data', ['user' => $user, 'hasRoles'  => $hasRoles]);

        }

        catch(ModelNotFoundException $modelNotFoundException)
        {
            $error = trans('errors.user_not_found');

            return sendError($error['message'], [], $error['status_code']);
        }


        catch (\Exception $e)
        {
            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try
        {
            $user = $this->userService->getUserById($id);

            $roles = $this->roleService->getAllRoles();

            $hasRoles = $user->roles()->pluck('name');

            $data = [
                'user'      => $user,
                'roles'     => $roles,
                'hasRoles'  => $hasRoles
            ];

            return sendResponse('Edit User Form', $data);

        }

        catch(ModelNotFoundException $modelNotFoundException)
        {
            $error = trans('errors.user_not_found');

            return sendError($error['message'], [], $error['status_code']);
        }

        catch (\Exception $e)
        {
            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try
        {

            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|min:2|max:255',
                'email' => 'nullable|email|max:255|unique:users,email,'. $id . ',id',
                'password' => 'nullable|string|min:6|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).+$/',
                'confirm_password' => 'nullable|string|same:password|min:6|max:255',
                'roles' => 'required|array|min:1',  // Ensure that roles are provided
                'roles.*' => 'exists:roles,name',   // Ensure each role exists in the roles table
            ]);

            if ($validator->fails())
            {
                $error = trans('errors.validation_error');

                return sendError($error['message'], $validator->errors()->toArray(), $error['status_code']);
            }

            $data = $validator->validated();

            if ($data['password'] == null)
            {
                unset($data['password'], $data['confirm_password']);
            }

            $updatedUser = $this->userService->updateUser($id, $data);

            if ($updatedUser)
            {
                return sendResponse('User is updated successfully', $updatedUser->toArray());
            }

        }

        catch(ModelNotFoundException $modelNotFoundException)
        {
            $error = trans('errors.user_not_found');

            return sendError($error['message'], [], $error['status_code']);
        }

        catch (\Exception $e)
        {
            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try
        {

            $result = $this->userService->deleteUser($id);

            if ($result)
            {
                return sendResponse('User is deleted successfully');
            }

        }

        catch(ModelNotFoundException $modelNotFoundException)
        {
            $error = trans('errors.user_not_found');

            return sendError($error['message'], [], $error['status_code']);
        }

        catch (\Exception $e)
        {
            $error = trans('errors.server_error');

            return sendError($error['message'], [], $error['status_code']);
        }

    }


}
