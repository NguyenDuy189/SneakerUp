@extends('layouts.admin')

@section('title', 'Sửa/Duyệt Đánh Giá')

@section('content')
<div class="form-container">
    <h2>Chi Tiết Đánh Giá #{{ $review->id }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Sản phẩm:</label>
            <input type="text" value="{{ $review->product->name ?? 'N/A' }}" disabled>
        </div>

        <div class="form-group">
            <label>Người đánh giá:</label>
            <input type="text" value="{{ $review->user->fullname ?? 'N/A' }}" disabled>
        </div>

       <div class="form-group">
    <label for="rating">Xếp hạng (1-5)</label>
    <input type="number" id="rating" name="rating" value="{{ $review->rating }}" disabled>
</div>

<div class="form-group">
    <label for="comment">Bình luận</label>
    <textarea id="comment" name="comment" rows="5" disabled>{{ $review->comment }}</textarea>
</div>
<div class="form-group">
            <label for="admin_reply">Phản Hồi Của Admin (Sẽ hiển thị công khai)</label>
            <textarea id="admin_reply" name="admin_reply" rows="4"
                      placeholder="VD: Cảm ơn bạn đã đánh giá. Shop sẽ cải thiện...">{{ old('admin_reply', $review->admin_reply) }}</textarea>
        </div>
        <div class="form-group">
    <label for="status">Trạng Thái</label>
    {{-- SỬA LẠI CÁC OPTION --}}
    <select id="status" name="status" required>
        <option value="visible" {{ old('status', $review->status) == 'visible' ? 'selected' : '' }}>Hiển thị (Visible)</option>
        <option value="hidden" {{ old('status', $review->status) == 'hidden' ? 'selected' : '' }}>Ẩn (Hidden)</option>
    </select>
</div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection
