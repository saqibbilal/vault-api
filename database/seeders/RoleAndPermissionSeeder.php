<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear the cache to prevent "Permission not found" errors
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define Permissions
        $permissions = [
            'view-docs',
            'upload-docs',
            'edit-docs',
            'delete-docs',
            'view-audit-logs'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }

        if (!Role::where('name', 'super-admin')->where('guard_name', 'api')->exists()) {
            Role::create(['name' => 'super-admin', 'guard_name' => 'api'])
                ->givePermissionTo(Permission::all());
        }

        if (!Role::where('name', 'auditor')->where('guard_name', 'api')->exists()) {
            Role::create(['name' => 'auditor', 'guard_name' => 'api'])
                ->givePermissionTo(['view-docs', 'view-audit-logs']);
        }

        if (!Role::where('name', 'contributor')->where('guard_name', 'api')->exists()) {
            Role::create(['name' => 'contributor', 'guard_name' => 'api'])
                ->givePermissionTo(['view-docs', 'upload-docs', 'edit-docs']);
        }

    }
}
