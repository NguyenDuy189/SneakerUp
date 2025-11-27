@extends('layouts.account')

@section('title', 'Thông Tin Tài Khoản')

@section('account_content')

    {{--
        Chúng ta sẽ bọc các form của Breeze (partials)
        trong các thẻ div mới để CSS của chúng ta có thể
        kiểm soát được chúng.
    --}}

    <div class="breeze-forms-container">

        <div class="form-card" id="update-profile-card">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="form-card" id="update-password-card">
            @include('profile.partials.update-password-form')
        </div>

        <div class="form-card" id="delete-account-card">
            @include('profile.partials.delete-user-form')
        </div>

    </div>

@endsection
