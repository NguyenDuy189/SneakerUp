<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthController
 *
 * Xử lý đăng nhập, đăng xuất cho hệ thống SneakerUp.
 * Hỗ trợ login bằng username hoặc email.
 */
class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập người dùng.
     */
    public function login(Request $request)
    {
        // ✅ 1. Kiểm tra dữ liệu nhập
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');
        $loginInput = $request->input('username');

        // ✅ 2. Xác định đăng nhập bằng email hay username
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $loginInput,
            'password' => $request->input('password'),
        ];

        // ✅ 3. Thử đăng nhập
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // ✅ 4. Kiểm tra phân quyền truy cập
            if (in_array($user->role, ['admin', 'staff'], true)) {
                $request->session()->regenerate();

                return redirect()
                    ->route('admin.dashboard.index')
                    ->with('success', '🎉 Đăng nhập thành công! Chào mừng bạn trở lại, ' . $user->name . '.');
            }

            // ❌ Nếu không phải admin/staff
            Auth::logout();
            return back()->withErrors([
                'username' => '🚫 Tài khoản của bạn không có quyền truy cập khu vực quản trị.',
            ])->onlyInput('username');
        }

        // ❌ Sai tài khoản hoặc mật khẩu
        return back()->withErrors([
            'username' => '⚠️ Sai tài khoản hoặc mật khẩu. Vui lòng thử lại.',
        ])->onlyInput('username');
    }

    /**
     * Đăng xuất khỏi hệ thống.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', '👋 Bạn đã đăng xuất thành công!');
    }
}
