@extends('layouts.admin')

@section('title', 'Thêm Mới Bài Viết')

@section('content')
<div class="form-container">
    <h2>Thêm Mới Bài Viết</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="title">Tiêu Đề Bài Viết</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div class="form-group">
            <label for="post_category_id">Danh Mục</label>
            <select id="post_category_id" name="post_category_id" required>
                <option value="">-- Chọn danh mục --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('post_category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="content">Nội Dung</label>
            <textarea id="content" name="content" rows="10" required>{{ old('content') }}</textarea>
        </div>

        <div class="form-group">
            <label for="image">Ảnh Bìa (Tùy chọn)</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <div class="form-group">
            <label for="status">Trạng Thái</label>
            <select id="status" name="status" required>
                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Bản nháp (Draft)</option>
                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Công khai (Published)</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection
