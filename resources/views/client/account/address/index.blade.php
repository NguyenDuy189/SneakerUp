@extends('layouts.account')

@section('title', 'Sổ Địa Chỉ')

@section('account_content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Sổ Địa Chỉ Của Tôi</h2>
        <a href="{{ route('client.address.create') }}" class="btn btn-primary" style="background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
            + Thêm Địa Chỉ Mới
        </a>
    </div>
    <p>Quản lý các địa chỉ giao hàng của bạn.</p>

    @if (session('success'))
        <div class="alert alert-success" style="padding: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="address-list" style="margin-top: 20px;">
        @forelse ($addresses as $address)
            <div class="address-item" style="border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-weight: bold; font-size: 1.1em;">
                        {{ $address->fullname }}
                        @if($address->is_default)
                            <span class="status-tag status-active" style="padding: 3px 8px; border-radius: 10px; font-size: 12px; color: white; background-color: #28a745; margin-left: 10px;">Mặc định</span>
                        @endif
                    </div>
                    <p style="color: #6c757d; margin: 5px 0 0 0;">{{ $address->phone }}</p>
                    <p style="color: #6c757d; margin: 5px 0 0 0;">{{ $address->address }}</p>
                </div>
                <div class="address-actions" style="display: flex; gap: 10px; margin-top: 10px; white-space: nowrap;">

                    <a href="{{ route('client.address.edit', $address->id) }}" style="color: #007bff; text-decoration: none;">Sửa</a>

                    <form action="{{ route('client.address.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này?');" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 0;">Xóa</button>
                    </form>

                    @if(!$address->is_default)
                        <form action="{{ route('client.address.setDefault', $address->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" style="background: none; border: none; color: #28a745; cursor: pointer; padding: 0;">Đặt làm mặc định</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p>Bạn chưa lưu địa chỉ nào.</p>
        @endforelse
    </div>
</div>
@endsection
