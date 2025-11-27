<section>
    <header>
        <h2>
            Thông Tin Tài Khoản
        </h2>

        <p>
            Cập nhật thông tin hồ sơ và địa chỉ email của bạn.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div>
            <label for="fullname">Họ và Tên</label>
            <input id="fullname" name="fullname" type="text" value="{{ old('fullname', $user->fullname) }}" required autofocus autocomplete="name">
            @error('fullname')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" disabled>
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div classs="email-verification-warning">
                    <p>
                        Email của bạn chưa được xác thực.

                        <button form="send-verification" class="btn-link">
                            Bấm vào đây để gửi lại email xác thực.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="form-success-message">
                            Một liên kết xác thực mới đã được gửi đến email của bạn.
                        </p>
                    @endif
                </div>
            @endif
            </div>

        <div>
            <label for="phone">Số điện thoại</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" autocomplete="tel">
            @error('phone')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>


        <div style="display: flex; align-items: center; gap: 15px;">
            <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm">Đã lưu.</p>
            @endif
        </div>
    </form>
</section>
