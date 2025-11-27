@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Danh Mục')

@section('content')
<div class="form-container">
    <h2>Chỉnh Sửa Danh Mục: {{ $postCategory->name }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.post-categories.update', $postCategory->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Tên Danh Mục</label>
            <input type="text" id="name" name="name" value="{{ old('name', $postCategory->name) }}" required>

            @error('name')
                <div class="alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
             <label for="status" style="display: inline-flex; align-items: center; font-weight: normal; gap: 10px;">
                <input type="checkbox" id="status" name="status" value="1" {{ old('status', $postCategory->status) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                <span>Hiển thị (Active)</span>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.post-categories.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection
