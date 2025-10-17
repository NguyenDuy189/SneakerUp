<?php

namespace App\Http\Controllers\Admin;

// QUAN TRỌNG NHẤT: Đảm bảo 2 dòng này tồn tại
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate; // Sử dụng Gate để có thể authorize()
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// QUAN TRỌNG NHẤT: Đảm bảo class này "extends Controller"
class UserController extends Controller
{
     use AuthorizesRequests;
    public function index(Request $request)
    {
        // Bây giờ, hàm authorize() sẽ hoạt động
        $this->authorize('user-list');
        $search = $request->input('search');
        $query = User::query();
        if ($search) {
            $query->where('fullname', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
        }
        $users = $query->with('roles')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('user-create');
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('user-create');
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required',
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20'
        ]);
        $baseRole = 'staff';
        $user = User::create([
            'fullname' => $request->fullname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $baseRole,
            'status' => $request->status,
        ]);
        $user->assignRole($request->input('role'));
        return redirect()->route('admin.users.index')->with('success', 'Tạo tài khoản thành công!');
    }

    public function show(User $user)
    {
        $this->authorize('user-list');
        if ($user->role === 'customer') {
            $orders = $user->orders()->latest()->get();
            return view('admin.customers.show', ['customer' => $user, 'orders' => $orders]);
        }
        return redirect()->route('admin.users.edit', $user->id);
    }

    public function edit(User $user)
    {
        $this->authorize('user-edit');
        if ($user->role === 'customer') {
            return view('admin.customers.edit', ['customer' => $user]);
        }
        $roles = Role::all();
        $userRoles = $user->getRoleNames();
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('user-edit');
        if ($user->role === 'customer') {
            $request->validate([
                'fullname' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'status' => 'required|in:active,inactive',
            ]);
            $user->update($request->only('fullname', 'phone', 'status'));
            return redirect()->route('admin.users.index')->with('success', 'Cập nhật thông tin khách hàng thành công!');
        }
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required',
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20'
        ]);
        if ($user->hasRole(Role::SUPER_ADMIN)) {
            $newRoleName = $request->input('role');
            if ($newRoleName !== Role::SUPER_ADMIN) {
                if (User::role(Role::SUPER_ADMIN)->count() <= 1) {
                    return redirect()->back()->with('error', 'Không thể thay đổi vai trò của tài khoản Super Admin cuối cùng.');
                }
            }
        }
        $data = $request->except(['password', 'role']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        $user->syncRoles($request->input('role'));
        return redirect()->route('admin.users.index')->with('success', 'Cập nhật tài khoản thành công!');
    }

    public function destroy(User $user)
    {
        $this->authorize('user-delete');
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Bạn không thể xóa chính tài khoản của mình.');
        }
        if ($user->hasRole(Role::SUPER_ADMIN)) {
            if (User::role(Role::SUPER_ADMIN)->count() <= 1) {
                return redirect()->route('admin.users.index')->with('error', 'Không thể xóa tài khoản Super Admin cuối cùng.');
            }
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Xóa tài khoản thành công!');
    }
}
