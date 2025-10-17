<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa VÀ có vai trò là 'admin' không
        if (Auth::check() && Auth::user()->role == 'admin') {
            // Nếu đúng, cho phép request đi tiếp
            return $next($request);
        }

        // Nếu không đúng, chuyển hướng họ về trang đăng nhập admin
        return redirect()->route('admin.login.form')->withErrors([
            'email' => 'Bạn không có quyền truy cập vào khu vực này.',
        ]);
    }
}
