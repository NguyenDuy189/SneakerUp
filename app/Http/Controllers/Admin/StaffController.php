<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; 
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = User::where('role', '=', 'staff');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('fullname', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        $users = $query->with('roles')->orderBy('id', 'desc')->paginate(10);
        return view('admin.staff.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('name', '!=', Role::SUPER_ADMIN)->get();
        return view('admin.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'role' => 'required',
            'status' => 'required|in:active,inactive'
        ]);
        $user = User::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'staff',
            'status' => $request->status,
        ]);
        $user->assignRole($request->input('role'));
        return redirect()->route('admin.staff.index')->with('success', 'Tạo tài khoản nhân viên thành công!');
    }

    public function edit(User $staff)
    {
        $roles = Role::where('name', '!=', Role::SUPER_ADMIN)->get();
        $userRoles = $staff->getRoleNames();
        return view('admin.staff.edit', compact('staff', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $staff)
{
    $request->validate([
        'fullname' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $staff->id,
        'username' => 'required|string|max:255|unique:users,username,' . $staff->id,
        'password' => 'nullable|string|min:6',
        'role' => 'required',
        'status' => 'required|in:active,inactive'
    ]);

    // === LỚP BẢO VỆ CHÍ MẠNG NẰM Ở ĐÂY ===
    if ($staff->hasRole(Role::SUPER_ADMIN)) {
        $newRoleName = $request->input('role');
        if ($newRoleName !== Role::SUPER_ADMIN) {
            $superAdminCount = User::role(Role::SUPER_ADMIN)->count();
            if ($superAdminCount <= 1) {
                return redirect()->back()->with('error', 'Không thể thay đổi vai trò của tài khoản Super Admin cuối cùng.');
            }
        }
    }

    $data = $request->except(['password', 'role']);

    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    $staff->update($data);
    $staff->syncRoles($request->input('role'));

    return redirect()->route('admin.staff.index')->with('success', 'Cập nhật thông tin nhân viên thành công!');
}
    /**
     * SỬA LẠI: Nâng cấp logic xóa để an toàn hơn
     */
    public function destroy(User $staff)
    {
        // 1. Luôn ngăn người dùng tự xóa chính mình
        if ($staff->id === auth()->id()) {
            return redirect()->route('admin.staff.index')->with('error', 'Bạn không thể xóa chính tài khoản của mình.');
        }

        // 2. Kiểm tra nếu tài khoản sắp bị xóa là Super Admin
        if ($staff->hasRole(Role::SUPER_ADMIN)) {
            // Đếm xem có bao nhiêu Super Admin khác trong hệ thống
            $superAdminCount = User::role(Role::SUPER_ADMIN)->count();

            // Nếu chỉ còn 1 và đó chính là người sắp bị xóa, thì từ chối
            if ($superAdminCount <= 1) {
                return redirect()->route('admin.staff.index')->with('error', 'Không thể xóa tài khoản Super Admin cuối cùng.');
            }
        }

        try {
            $staff->delete();
            return redirect()->route('admin.staff.index')->with('success', 'Xóa tài khoản nhân viên thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.staff.index')->with('error', 'Không thể xóa tài khoản này.');
        }
    }
}
