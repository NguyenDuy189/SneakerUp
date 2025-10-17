@extends('layouts.admin')
@section('title', 'Sửa Vai Trò')

@section('content')
<div class="form-container">
    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Tên vai trò</label>
            <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" required>
            @error('name')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Quyền hạn (Permissions)</label>
            <div class="permission-groups-container">
                <div class="master-checkbox-container">
                    <input type="checkbox" id="checkAll">
                    <label for="checkAll"><strong>Chọn tất cả / Bỏ chọn tất cả</strong></label>
                </div>

                @foreach ($groupedPermissions as $groupName => $permissionsInGroup)
                    <div class="permission-group">
                        <h4 class="permission-group-title">
                            {{-- === SỬA LỖI UX NẰM Ở ĐÂY === --}}
                            {{-- Bọc cả input và chữ vào trong một thẻ <label> duy nhất --}}
                            {{-- Giờ đây người dùng có thể bấm vào cả chữ "Quản lý Product" --}}
                            <label>
                                <input type="checkbox" class="group-checkbox" data-group="{{ $groupName }}">
                                <strong>Quản lý {{ ucfirst($groupName) }}</strong>
                            </label>
                        </h4>
                        <div class="permission-grid">
                            @foreach ($permissionsInGroup as $permission)
                                <div class="permission-item">
                                    <input type="checkbox" id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}"
                                        class="permission-checkbox group-{{ $groupName }}"
                                        {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                    <label for="permission_{{ $permission->id }}">{{ $permissionLabels[$permission->name] ?? $permission->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection
