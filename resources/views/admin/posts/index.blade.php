@extends('layouts.admin')

@section('title', 'Danh Sách Bài Viết')

@section('content')

<style>
    /* Chúng ta vẫn cần code này để xóa margin của form.
      Bạn có thể để nó ở đây hoặc chuyển vào file CSS chính.
    */
    .action-buttons form {
        margin: 0 !important;
    }
</style>
<div class="table-container">
    <div class="table-header">
        <form action="{{ route('admin.posts.index') }}" method="GET" class="search-container">
            <input type="text" name="search" placeholder="Tìm kiếm bài viết..." value="{{ request('search') }}">
            <button type="submit" class="search-button">
                <i class="fas fa-search"></i>
            </button>
        </form>

        @can('post-create')
            <a href="{{ route('admin.posts.create') }}" class="add-new-btn">+ Thêm bài viết</a>
        @endcan
    </div>


    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ảnh</th>
                <th>Tiêu Đề</th>
                <th>Danh Mục</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>
                        @if($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" alt="Ảnh bìa" width="70">
                        @else
                            <small>N/A</small>
                        @endif
                    </td>
                    <td>{{ $post->title }}</td>
                    <td>
                        {{ $post->category->name ?? 'N/A' }}
                    </td>
                    <td>
                        @if($post->status == 'published')
                            <span class="status-tag status-active">Đã đăng</span>
                        @else
                            <span class="status-tag status-inactive">Bản nháp</span>
                        @endif
                    </td>

                    <td> <div class="action-buttons"> @can('post-edit')
                                <a href="{{ route('admin.posts.edit', $post->id) }}" class="action-icon edit-icon" title="Sửa"><i class="fas fa-edit"></i></a>
                            @endcan

                            @can('post-delete')
                                <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?');">
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
                    <td colspan="6" style="text-align: center;">Không tìm thấy bài viết nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-container">
        {{ $posts->links() }}
    </div>
</div>
@endsection
