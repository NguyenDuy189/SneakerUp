<?php

namespace Database\Seeders;

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
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Tạo các Quyền Hạn
        $permissions = [
            'product-list', 'product-create', 'product-edit', 'product-delete',
            'order-list', 'order-view', 'order-update-status',
            'user-list', 'user-create', 'user-edit', 'user-delete',
            'role-list', 'role-create', 'role-edit', 'role-delete'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Tạo vai trò Super Admin và gán TẤT CẢ quyền hạn
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Gán vai trò Super Admin cho user có id = 2 (tài khoản admin của bạn)
        $adminUser = \App\Models\User::find(2);
        if ($adminUser) {
            $adminUser->assignRole($superAdminRole);
        }
    }
}
