{{-- File: resources/views/admin/roles/create.blade.php --}}

@extends('layouts.admin')
@section('title', 'Thêm Vai Trò Mới')

@section('content')
<div class="form-container">
    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Tên vai trò</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- THÊM TOÀN BỘ KHỐI CHỌN QUYỀN HẠN VÀO ĐÂY --}}
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
                            <label>
                                <input type="checkbox" class="group-checkbox" data-group="{{ $groupName }}">
                                <strong>Quản lý {{ ucfirst($groupName) }}</strong>
                            </label>
                        </h4>
                        <div class="permission-grid">
                            @foreach ($permissionsInGroup as $permission)
                                <div class="permission-item">
                                    <input type="checkbox" id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}"
                                        class="permission-checkbox group-{{ $groupName }}">
                                    <label for="permission_{{ $permission->id }}">{{ $permissionLabels[$permission->name] ?? $permission->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection
