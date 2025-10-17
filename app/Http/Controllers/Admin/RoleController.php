<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// SỬA LỖI 1: Xóa import bị trùng lặp, chỉ giữ lại model tùy chỉnh của chúng ta
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

   public function create()
{
    // Lấy tất cả các quyền hạn, giống hệt như hàm edit()
    $permissions = Permission::all();
    $permissionLabels = $this->getPermissionLabels();
    $groupedPermissions = $this->groupPermissions($permissions);

    // Gửi tất cả dữ liệu này ra view 'create'
    return view('admin.roles.create', compact('permissions', 'permissionLabels', 'groupedPermissions'));
}
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name'
        ], [
            'name.required' => 'Tên vai trò là bắt buộc.',
            'name.unique' => 'Tên vai trò này đã tồn tại.'
        ]);

        $permissions = $request->input('permissions', []);

        $totalPermissionsCount = Permission::count();
        if (count($permissions) === $totalPermissionsCount) {
            return redirect()->back()->withInput()->with('error', 'Không thể tạo một vai trò mới có toàn quyền hệ thống.');
        }

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Tạo vai trò mới thành công!');
    }

    public function edit(Role $role)
    {
        // SỬA LỖI 2: Sử dụng hằng số Role::SUPER_ADMIN thay vì chuỗi 'Super Admin'
        if ($role->name === Role::SUPER_ADMIN) {
            return redirect()->route('admin.roles.index')->with('error', 'Không thể sửa vai trò hệ thống.');
        }

        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissionLabels = $this->getPermissionLabels();
        $groupedPermissions = $this->groupPermissions($permissions);

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions', 'permissionLabels', 'groupedPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id
        ]);

        $permissions = $request->input('permissions', []);

        $totalPermissionsCount = Permission::count();
        if ($role->name !== Role::SUPER_ADMIN && count($permissions) === $totalPermissionsCount) {
             return redirect()->back()->withInput()->with('error', 'Không thể gán toàn quyền hệ thống cho vai trò này.');
        }

        $role->update(['name' => $request->name]);
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Cập nhật vai trò và quyền hạn thành công!');
    }

    public function destroy(Role $role)
    {
        if ($role->name === Role::SUPER_ADMIN) {
            return redirect()->route('admin.roles.index')->with('error', 'Không thể xóa vai trò hệ thống.');
        }

        try {
            $role->delete();
            return redirect()->route('admin.roles.index')->with('success', 'Xóa vai trò thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.roles.index')->with('error', 'Không thể xóa vai trò này.');
        }
    }

    // === CÁC HÀM PHỤ TRỢ (PRIVATE HELPERS) ===
    private function groupPermissions($permissions)
    {
        $groupedPermissions = [];
        foreach ($permissions as $permission) {
            $groupName = explode('-', $permission->name)[0];
            $groupedPermissions[$groupName][] = $permission;
        }
        return $groupedPermissions;
    }

    private function getPermissionLabels()
    {
        return [
            'product-list' => 'Xem danh sách sản phẩm',
            'product-create' => 'Thêm sản phẩm mới',
            'product-edit' => 'Sửa sản phẩm',
            'product-delete' => 'Xóa sản phẩm',
            'order-list' => 'Xem danh sách đơn hàng',
            'order-view' => 'Xem chi tiết đơn hàng',
            'order-update-status' => 'Cập nhật trạng thái đơn hàng',
            'user-list' => 'Xem danh sách người dùng',
            'user-create' => 'Tạo người dùng mới',
            'user-edit' => 'Sửa thông tin người dùng',
            'user-delete' => 'Xóa người dùng',
            'role-list' => 'Xem danh sách vai trò',
            'role-create' => 'Tạo vai trò mới',
            'role-edit' => 'Sửa vai trò & gán quyền',
            'role-delete' => 'Xóa vai trò',
        ];
    }
}
