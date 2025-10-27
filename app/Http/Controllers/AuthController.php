<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $remember = $request->filled('remember');
        $loginInput = $request->input('username');

        // Nếu người dùng nhập email
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $loginInput,
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $remember)) {
            // Kiểm tra quyền
            if (Auth::user()->role === 'admin'||'staff') {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công!');
            } else {
                Auth::logout();
                return back()->withErrors(['username' => 'Bạn không có quyền truy cập admin']);
            }
        }

        return back()->withErrors(['username' => 'Sai tài khoản hoặc mật khẩu'])->onlyInput('username');
    }

    // Đăng xuất
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Đã đăng xuất!');
    }
}
