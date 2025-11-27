@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Bài Viết')

@section('content')
<div class="form-container">
    <h2>Chỉnh Sửa: {{ $post->title }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Tiêu Đề Bài Viết</label>
            <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required>
        </div>

        <div class="form-group">
            <label for="post_category_id">Danh Mục</label>
            <select id="post_category_id" name="post_category_id" required>
                <option value="">-- Chọn danh mục --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('post_category_id', $post->post_category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="content">Nội Dung</label>
            <textarea id="content" name="content" rows="10" required>{{ old('content', $post->content) }}</textarea>
        </div>

        <div class="form-group">
            <label for="image">Ảnh Bìa (Để trống nếu không muốn thay)</label>
            <input type="file" id="image" name="image" accept="image/*">

            @if($post->image)
                <div style="margin-top: 10px;">
                    <img src="{{ asset('storage/' . $post->image) }}" alt="Ảnh bìa" height="100">
                    <br><small>Ảnh hiện tại</small>
                </div>
            @endif
        </div>

        <div class="form-group">
            <label for="status">Trạng Thái</label>
            <select id="status" name="status" required>
                <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Bản nháp (Draft)</option>
                <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Công khai (Published)</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection
