<?php

namespace App\Http\Controllers;

// Không cần ProfileUpdateRequest và Rule nữa
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     * (HÀM ĐÃ SỬA THEO LỰA CHỌN 2 - KHÓA EMAIL)
     */
    public function update(Request $request): RedirectResponse
    {
        // Lấy user đang đăng nhập
        $user = $request->user();

        // Validate dữ liệu (ĐÃ XÓA 'email')
        $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:15'],
        ]);

        // Cập nhật dữ liệu (CHỈ CẬP NHẬT 2 TRƯỜNG NÀY)
        $user->fullname = $request->fullname;
        $user->phone = $request->phone;

        // (ĐÃ XÓA LOGIC SỬA EMAIL VÀ 'email_verified_at')

        // Lưu vào CSDL
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
