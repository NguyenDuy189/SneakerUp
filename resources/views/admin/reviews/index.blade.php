@extends('layouts.admin')

@section('title', 'Quản Lý Đánh Giá')

@section('content')
<div class="table-container">
    <form action="{{ route('admin.reviews.index') }}" method="GET" class="search-container">
    {{-- SỬA LẠI BỘ LỌC --}}
    <select name="status" onchange="this.form.submit()" style="width: 200px; padding: 8px; border-radius: 20px; border: 1px solid #ddd;">
        <option value="">-- Lọc theo trạng thái --</option>
        <option value="visible" {{ request('status') == 'visible' ? 'selected' : '' }}>Đang hiển thị (Visible)</option>
        <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Đang ẩn (Hidden)</option>
    </select>
</form>
        {{-- Admin không "thêm" đánh giá --}}
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Sản phẩm</th>
                <th>Người viết</th>
                <th>Rating</th>
                <th>Bình luận</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reviews as $review)
                <tr>
                    <td>{{ $review->id }}</td>
                    <td>{{ $review->product->name ?? 'N/A' }}</td>
                    <td>{{ $review->user->fullname ?? 'N/A' }}</td>
                    <td>{{ $review->rating }} ★</td>
                    <td>{{ Str::limit($review->comment, 50) }}</td>
                    <td>
                        {{-- SỬA LẠI CÁCH HIỂN THỊ TAG --}}
    @if($review->status == 'visible')
        <span class="status-tag status-active">Hiển thị</span>
    @else
        <span class="status-tag status-inactive">Đang ẩn</span>
    @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            @can('review-edit')
                                <a href="{{ route('admin.reviews.edit', $review->id) }}" class="action-icon edit-icon" title="Sửa / Duyệt"><i class="fas fa-edit"></i></a>
                            @endcan
                            @can('review-delete')
                                <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-icon delete-icon" title="Xóa"><i class="fas fa-trash"></i></button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Không tìm thấy đánh giá nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-container">
        {{ $reviews->appends(request()->query())->links() }}
    </div>
</div>
@endsection
