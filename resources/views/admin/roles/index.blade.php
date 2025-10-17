@extends('layouts.admin')
@section('title', 'Quản Lý Vai Trò & Quyền Hạn')

@section('content')
<div class="table-container">
    <div class="table-header">
        @can('role-create')
            <a href="{{ route('admin.roles.create') }}" class="add-new-btn">+ Thêm vai trò mới</a>
        @endcan
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên vai trò</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td class="action-buttons">
                        @if ($role->name !== \App\Models\Role::SUPER_ADMIN)
                            @can('role-edit')
                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="action-icon edit-icon" title="Sửa"><i class="fas fa-edit"></i></a>
                            @endcan
                            @can('role-delete')
                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vai trò này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-icon delete-icon" title="Xóa"><i class="fas fa-trash"></i></button>
                                </form>
                            @endcan
                        @else
                            {{-- &nbsp; là một khoảng trắng để giữ cho ô không bị rỗng và giữ đúng chiều cao --}}
                            &nbsp;
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">Chưa có vai trò nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-container">
        {{ $roles->links() }}
    </div>
</div>
@endsection
