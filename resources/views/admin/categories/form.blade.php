@extends('admin.layouts.app') 

@section('title', $category->exists ? 'Cập nhật danh mục' : 'Tạo danh mục mới')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">@yield('title')</h4>
        </div>
        <div class="card-body">

            {{-- Hiển thị lỗi validation chung (nếu có) --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <p class="mb-0"><strong>Đã xảy ra lỗi:</strong></p>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form 
                action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" 
                method="POST" 
                enctype="multipart/form-data"> @csrf
                
                @if($category->exists)
                    @method('PUT') @endif

                <div class="row">
                    {{-- Cột trái: Thông tin chính --}}
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" 
                                value="{{ old('name', $category->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Danh mục cha</label>
                            <select class="form-select @error('parent_id') is-invalid @enderror" 
                                id="parent_id" name="parent_id">
                                <option value="">— Chọn làm danh mục gốc —</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}" 
                                        {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" 
                                rows="5">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Cột phải: Thumbnail, Status --}}
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status">
                                <option value="active" {{ old('status', $category->status) == 'active' ? 'selected' : '' }}>
                                    Hiển thị
                                </option>
                                <option value="inactive" {{ old('status', $category->status) == 'inactive' ? 'selected' : '' }}>
                                    Ẩn
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="thumbnail_file" class="form-label">Ảnh thumbnail</label>
                            
                            <input type="file" class="form-control @error('thumbnail_file') is-invalid @enderror" 
                                id="thumbnail_file" name="thumbnail_file" onchange="previewImage(event)">
                            
                            @error('thumbnail_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 text-center">
                            <img id="image-preview" 
                                src="{{ $category->thumbnail ?? 'https://via.placeholder.com/200x200?text=Chưa+có+ảnh' }}" 
                                alt="Xem trước" 
                                class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Lưu lại
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // JS xem trước ảnh
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('image-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection