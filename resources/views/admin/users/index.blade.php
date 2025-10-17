@extends('layouts.admin')

@section('title', 'Quản Lý Tất Cả Tài Khoản')

@section('content')
<div class="table-container">
    <div class="table-header">
        <form action="{{ route('admin.users.index') }}" method="GET" class="search-container">
            <input type="text" name="search" placeholder="Tìm kiếm tài khoản..." value="{{ request('search') }}">
            <button type="submit" class="search-button">
                <i class="fas fa-search"></i>
            </button>
        </form>
        @can('user-create')
            <a href="{{ route('admin.users.create') }}" class="add-new-btn">+ Thêm tài khoản</a>
        @endcan
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ và Tên</th>
                <th>Email</th>
                <th>Vai Trò</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->fullname }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @forelse ($user->getRoleNames() as $roleName)
                            <span class="role-tag role-{{ \Illuminate\Support\Str::slug($roleName) }}">{{ $roleName }}</span>
                        @empty
                            <span class="role-tag role-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                        @endforelse
                    </td>
                    <td>
                        <span class="status-tag status-{{ $user->status }}">{{ ucfirst($user->status) }}</span>
                    </td>
                    <td class="action-buttons">
                        {{-- Logic điều phối: Dựa vào vai trò để quyết định luồng xử lý --}}
                        @if ($user->role === 'customer')
                            {{-- Nếu là Khách hàng, tất cả các nút sẽ điều hướng sang luồng CustomerController --}}
                            <a href="{{ route('admin.customers.show', $user->id) }}" class="action-icon view-icon" title="Xem chi tiết khách hàng">
                                <i class="fas fa-eye"></i>
                            </a>
                            @can('user-edit')
                                {{-- SỬA LỖI ĐIỀU HƯỚNG: Nút Sửa/Khóa cũng phải trỏ về CustomerController --}}
                                <a href="{{ route('admin.customers.edit', $user->id) }}" class="action-icon edit-icon" title="Sửa thông tin (Khóa/Mở)">
                                    <i class="fas fa-user-lock"></i>
                                </a>
                            @endcan
                        @else
                            {{-- Nếu là tài khoản nội bộ, tất cả các nút sẽ giữ ở lại luồng UserController --}}
                            @can('user-edit')
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="action-icon edit-icon" title="Sửa"><i class="fas fa-edit"></i></a>
                            @endcan
                            @can('user-delete')
                                @if (auth()->id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-icon delete-icon" title="Xóa"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            @endcan
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Không tìm thấy tài khoản nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination-container">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>
@endsection
