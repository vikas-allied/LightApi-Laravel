<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'view_user']);
        Permission::create(['name' => 'create_user']);
        Permission::create(['name' => 'update_user']);
        Permission::create(['name' => 'delete_user']);
        Permission::create(['name' => 'user_list']);

        Permission::create(['name' => 'view_role']);
        Permission::create(['name' => 'create_role']);
        Permission::create(['name' => 'update_role']);
        Permission::create(['name' => 'delete_role']);
        Permission::create(['name' => 'role_list']);

        $superAdminRole = Role::create(['name' => config('roles.super_admin_role')]);
        $adminRole = Role::create(['name' => config('roles.admin_role')]);
        $userRole = Role::create(['name' => config('roles.default_user_role')]);

        $permissions = Permission::all();

        $superAdminRole->syncPermissions($permissions);

        $adminRole->syncPermissions($permissions);

        $userRole->givePermissionTo('user_list');

    }
}
