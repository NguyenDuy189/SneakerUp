<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Xử lý logic đăng nhập
     */
    public function login(Request $request)
    {
        // 1. Validate dữ liệu đầu vào (tùy chọn nhưng nên có)
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Lấy thông tin đăng nhập từ form
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            // === PHẦN NÂNG CẤP QUAN TRỌNG ===
            // Thêm 2 điều kiện kiểm tra vai trò và trạng thái
            'role' => 'admin',
            'status' => 'active',
        ];

        // 3. Thử đăng nhập với các điều kiện trên
        if (Auth::attempt($credentials)) {
            // Nếu đăng nhập THÀNH CÔNG (đúng email, pass, là admin, và đang active)
            $request->session()->regenerate();

            // Chuyển hướng đến trang dashboard admin
            return redirect()->route('admin.dashboard');
        }

        // 4. Nếu đăng nhập THẤT BẠI
        // Quay lại trang đăng nhập và báo lỗi
        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác hoặc bạn không có quyền truy cập.',
        ])->onlyInput('email');
    }
     /**
     * Hàm này để xử lý logic đăng xuất
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login.form'); // Chuyển hướng về trang đăng nhập admin
    }
}
