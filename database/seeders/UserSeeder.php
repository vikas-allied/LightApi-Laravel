<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $super_admin_user = User::create([
            'name' => 'Super Admin User',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('superadmin@123')
        ]);

        $super_admin_role = Role::where(['name' => config('roles.super_admin_role')])->first();

        $super_admin_user->assignRole($super_admin_role);

        $admin_user = User::create([
           'name' => 'Admin User',
           'email' => 'admin@gmail.com',
           'password' => Hash::make('admin@123')
        ]);

        $admin_role = Role::where(['name' => config('roles.admin_role')])->first();

        // permissions via role
        $admin_user->assignRole($admin_role);

        $normal_user = User::create([
           'name' => 'Test User',
           'email' => 'test@gmail.com',
           'password' => Hash::make('test@123')
        ]);

        $user_role = Role::where(['name' => config('roles.default_user_role')])->first();

        $normal_user->assignRole($user_role);


    }
}
