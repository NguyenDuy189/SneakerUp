<section>
    <header>
        <h2>
            Cập Nhật Mật Khẩu
        </h2>

        <p>
            Đảm bảo tài khoản của bạn sử dụng mật khẩu dài, ngẫu nhiên để giữ an toàn.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div>
            <label for="current_password">Mật khẩu hiện tại</label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="password">Mật khẩu mới</label>
            <input id="password" name="password" type="password" autocomplete="new-password">
            @error('password', 'updatePassword')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="password_confirmation">Xác nhận Mật khẩu mới</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: flex; align-items: center; gap: 15px;">
            <button type="submit" class="btn btn-primary">Lưu</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm"
                >Đã lưu.</p>
            @endif
        </div>
    </form>
</section>
