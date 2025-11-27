@extends('layouts.admin')

@section('title', 'Thêm Mới Danh Mục')

@section('content')
<div class="form-container">
    <h2>Thêm Mới Danh Mục Bài Viết</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.post-categories.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Tên Danh Mục</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>

            @error('name')
                <div class="alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="status" style="display: inline-flex; align-items: center; font-weight: normal; gap: 10px;">
                <input type="checkbox" id="status" name="status" value="1" checked style="width: 18px; height: 18px;">
                <span>Hiển thị (Active)</span>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="{{ route('admin.post-categories.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection
