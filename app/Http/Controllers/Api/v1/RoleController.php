<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\Service\RoleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public static function middleware()
    {
        return [

            new Middleware('permission:role_list', only: ['index']),
            new Middleware('permission:view_role', only: ['show']),
            new Middleware('permission:update_role', only: ['edit']),
            new Middleware('permission:create_role', only: ['create']),
            new Middleware('permission:delete_role', only: ['destroy']),
        ];
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $roles = $this->roleService->getAllRoles();

            return sendResponse('Role List', $roles->toArray());

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

            $permissions = Permission::select('id', 'name')->orderBy('name', 'ASC')->get();

            $data = ['permissions' => $permissions];

            return sendResponse('Create Role Form', $data);

        }catch (\Exception $e)
        {
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
                'permissions' => 'required|array|min:1',        // Ensure that permission are provided
                'permissions.*' => 'exists:permissions,name',   // Ensure each permission exists in the permissions table
            ]);

            if ($validator->fails()) {

                $error = trans('errors.validation_error');

                return sendError($error['message'], $validator->errors()->toArray(), $error['status_code']);
            }

            $data = $validator->validated();

            /*$role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web'
            ]);

            $role->syncPermissions($data['permissions']);*/

            $role = [
                'name' => $data['name'],
                'guard_name' => 'web'
            ];

            $role = $this->roleService->addRole($role, $data['permissions']);

            return sendResponse('Role is created successfully', $role->toArray());

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

            $role = Role::findOrFail($id);

            $roleHasPermissions = $role->permissions->pluck('name');

            $data = ['role' => $role, 'role_has_permissions' => $roleHasPermissions];

            return sendResponse('Role Info', $data);

        }

        catch(ModelNotFoundException $modelNotFoundException) {

            \Log::info($modelNotFoundException->getMessage());

            $error = trans('errors.role_not_found');

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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {

                $permissions = Permission::select('id', 'name')->orderBy('name', 'ASC')->get();

                $role = Role::findOrFail($id);

                $roleHasPermissions = $role->permissions->pluck('name');

                $data = ['role' => $role, 'permissions' => $permissions, 'role_has_permissions' => $roleHasPermissions];

                return sendResponse('Edit Role Form', $data);

            }

            catch(ModelNotFoundException $modelNotFoundException) {

                \Log::info($modelNotFoundException->getMessage());

                $error = trans('errors.role_not_found');

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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|min:2|max:255',
                'permissions' => 'required|array|min:1',        // Ensure that permission are provided
                'permissions.*' => 'exists:permissions,name',   // Ensure each permission exists in the permissions table
            ]);

            if ($validator->fails()) {

                $error = trans('errors.validation_error');

                return sendError($error['message'], $validator->errors()->toArray(), $error['status_code']);
            }

                $data = $validator->validated();


                $role = Role::findOrFail($id);

                $role->update($data);

                $role->syncPermissions($data['permissions']);

                return sendResponse('Role is updated successfully', $role->toArray());

            }

            catch (ModelNotFoundException $modelNotFoundException) {

                \Log::info($modelNotFoundException->getMessage());

                $error = trans('errors.role_not_found');

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

                $role = Role::findOrFail($id);

                $count = $role->users()->count();

                if ($count > 0) {

                    return sendError('There are users associated with the given role', [], 500);

                }

                $role->delete();

                return sendResponse('Role is successfully Deleted');

            }catch(ModelNotFoundException $modelNotFoundException) {

                \Log::info($modelNotFoundException->getMessage());

                $error = trans('errors.role_not_found');

                return sendError($error['message'], [], $error['status_code']);
            }catch (\Exception $e)
            {
                \Log::info($e->getMessage());

                $error = trans('errors.server_error');

                return sendError($error['message'], [], $error['status_code']);
            }

    }

}
