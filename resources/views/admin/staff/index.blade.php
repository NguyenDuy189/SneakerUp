@extends('layouts.admin')

@section('title', 'Quản Lý Nhân Viên')

@section('content')
<div class="table-container">
    <div class="table-header">
        {{-- Phần tìm kiếm đã đúng, giữ nguyên --}}
        <form action="{{ route('admin.staff.index') }}" method="GET" class="search-container">
            <input type="text" name="search" placeholder="Tìm kiếm nhân viên..." value="{{ request('search') }}">
            <button type="submit" class="search-button">
                <i class="fas fa-search"></i>
            </button>
        </form>

        @can('user-create')
            <a href="{{ route('admin.staff.create') }}" class="add-new-btn">+ Thêm nhân viên</a>
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
                        {{-- SỬA LỖI QUAN TRỌNG: Tạo class CSS động cho từng vai trò --}}
                        @if ($user->getRoleNames()->isEmpty())
                            <span class="role-tag role-default">Chưa có</span>
                        @else
                            @foreach ($user->getRoleNames() as $roleName)
                                @php
                                    // Chuyển đổi tên vai trò thành một class CSS an toàn
                                    // Ví dụ: "Biên Tập Viên" -> "bien-tap-vien"
                                    $roleClass = Str::slug($roleName);
                                @endphp
                                <span class="role-tag role-{{ $roleClass }}">{{ $roleName }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        {{-- Cấu trúc này đã rất tốt, giữ nguyên --}}
                        <span class="status-tag status-{{ $user->status }}">{{ ucfirst($user->status) }}</span>
                    </td>
                    <td class="action-buttons">
                        @can('user-edit')
                            <a href="{{ route('admin.staff.edit', $user->id) }}" class="action-icon edit-icon" title="Sửa"><i class="fas fa-edit"></i></a>
                        @endcan

                        @can('user-delete')
                            {{-- Logic ngăn tự xóa tài khoản rất tốt, giữ nguyên --}}
                            @if (auth()->id() !== $user->id)
                                <form action="{{ route('admin.staff.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân viên này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-icon delete-icon" title="Xóa"><i class="fas fa-trash"></i></button>
                                </form>
                            @endif
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Không tìm thấy nhân viên nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-container">
        {{ $users->links() }}
    </div>
</div>
@endsection
