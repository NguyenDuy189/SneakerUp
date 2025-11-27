@extends('layouts.client')

{{--
    File này sẽ kế thừa Header/Footer từ 'layouts.client'.
    Nó sẽ thêm vào một sidebar menu và một vùng nội dung mới.
--}}

@section('content')
<div class="account-container">

    <aside class="account-sidebar">
        <nav class="account-sidebar-nav">
            <ul>
                <li class="{{ Route::is('profile.edit') ? 'active' : '' }}">
    <a href="{{ route('profile.edit') }}">
        <i class="fa fa-user"></i> Thông tin tài khoản
    </a>
</li>
                <li class="{{ Route::is('client.orders.*') ? 'active' : '' }}">
                    <a href="{{ route('client.orders.index') }}">
                        <i class="fa fa-box"></i> Lịch sử mua hàng
                    </a>
                </li>
                <li class="{{ Route::is('client.address.*') ? 'active' : '' }}">
                    <a href="{{ route('client.address.index') }}">
                        <i class="fa fa-map-marker-alt"></i> Sổ địa chỉ
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="fa fa-sign-out-alt"></i> Đăng xuất
                        </a>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

    <section class="account-content">
        @yield('account_content')
    </section>

</div>
@endsection
