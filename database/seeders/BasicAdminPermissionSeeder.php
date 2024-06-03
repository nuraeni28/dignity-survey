<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class BasicAdminPermissionSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        // create permissions
        $permissions = [
            'permission list',
            'permission create',
            'permission edit',
            'permission delete',
            'role list',
            'role create',
            'role edit',
            'role delete',
            'user list',
            'user create',
            'user edit',
            'user delete',
            'customer list',
            'customer create',
            'customer edit',
            'customer delete',
            'question list',
            'question create',
            'question edit',
            'question delete',
            'period list',
            'period create',
            'period edit',
            'period delete',
            'interview list',
            'interview create',
            'interview edit',
            'interview delete',
            'schedule list',
            'schedule create',
            'schedule edit',
            'schedule delete',
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        // gets all permissions via Gate::before rule; see AuthServiceProvider
        // create demo users

        $superAdminRole = Role::create(['name' => 'super-admin']);
        $user = \App\Models\User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@superadmin.com',
        ]);
        $user->assignRole($superAdminRole);

        $adminRole = Role::create(['name' => 'admin']);
        foreach ($permissions as $permission) {
            $adminRole->givePermissionTo($permission);
        }
        $user = \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
        ]);
        $user->assignRole($adminRole);

        $userRole = Role::create(['name' => 'user']);
        $user = \App\Models\User::factory()->create([
            'name' => 'User',
            'email' => 'user@user.com',
        ]);
        $user->assignRole($userRole);
    }
}