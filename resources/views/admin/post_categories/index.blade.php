@extends('layouts.admin')

@section('title', 'Danh Mục Bài Viết')

@section('content')
<div class="table-container">
    <div class="table-header">


        {{-- Nút Thêm Mới --}}
        @can('post-category-create')
            <a href="{{ route('admin.post-categories.create') }}" class="add-new-btn">+ Thêm danh mục</a>
        @endcan
    </div>


    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Danh Mục</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            {{-- Dùng @forelse để lặp và kiểm tra rỗng cùng lúc --}}
            @forelse ($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>
                        {{-- Hiển thị trạng thái --}}
                        @if($category->status)
                            <span class="status-tag status-active">Hoạt động</span>
                        @else
                            <span class="status-tag status-inactive">Đã ẩn</span>
                        @endif
                    </td>
                    <td class="action-buttons">
                        {{-- Nút Sửa --}}
                        @can('post-category-edit')
                            <a href="{{ route('admin.post-categories.edit', $category->id) }}" class="action-icon edit-icon" title="Sửa"><i class="fas fa-edit"></i></a>
                        @endcan

                        {{-- Nút Xóa (Dùng form) --}}
                        @can('post-category-delete')
                            <form action="{{ route('admin.post-categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-icon delete-icon" title="Xóa"><i class="fas fa-trash"></i></button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                {{-- Thông báo khi không có dữ liệu --}}
                <tr>
                    <td colspan="4" style="text-align: center;">Không tìm thấy danh mục nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Hiển thị phân trang --}}
    <div class="pagination-container">
        {{ $categories->links() }}
    </div>
</div>
@endsection
