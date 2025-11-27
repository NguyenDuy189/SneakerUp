<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User; // <-- Thêm dòng này

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
            'role-list', 'role-create', 'role-edit', 'role-delete',

            // Các quyền mới
            'post-category-list',
            'post-category-create',
            'post-category-edit',
            'post-category-delete',
            'post-list',
            'post-create',
            'post-edit',
            'post-delete',
            'review-list',
'review-edit',
'review-delete',
        ];

        foreach ($permissions as $permission) {
            // SỬA Ở ĐÂY: Dùng firstOrCreate() thay vì create()
            Permission::firstOrCreate(['name' => $permission]);
        }

        // SỬA Ở ĐÂY: Dùng firstOrCreate() thay vì create()
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        // Gán TẤT CẢ quyền hạn (bao gồm cả các quyền mới)
        $superAdminRole->givePermissionTo(Permission::all());

        // Gán vai trò Super Admin cho user có id = 2 (tài khoản admin của bạn)
        // SỬA Ở ĐÂY: Dùng User::find(2) vì đã import ở trên
        $adminUser = User::find(2);
        if ($adminUser) {
            // assignRole sẽ tự động bỏ qua nếu đã có vai trò này
            $adminUser->assignRole($superAdminRole);
        }
    }
}
