@extends('admin.layouts.app')

@section('title', 'Danh mục sản phẩm')

@section('styles')
    {{-- Thư viện Nestable (Kéo-thả) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.css">
    
    {{-- CSS tùy chỉnh cho cây danh mục --}}
    <style>
        .dd-handle {
            height: auto;
            padding: 10px 15px;
            font-weight: 600;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        .dd-item > button { display: none; } /* Ẩn nút expand/collapse mặc định */
        .dd-content {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border: 1px solid #dee2e6;
            border-top: none;
            background: #fff;
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
        }
        .dd-actions {
            margin-left: auto;
            white-space: nowrap;
        }
        .dd-actions .btn {
            margin-left: 5px;
        }
        .dd-placeholder { /* Vị trí rỗng khi kéo */
            flex: 1;
            margin: 5px 0;
            padding: 0;
            min-height: 30px;
            background: #f2f2f2;
            border: 1px dashed #b7b7b7;
            box-sizing: border-box;
        }
        .item-thumbnail {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 10px;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Quản lý Danh mục</h4>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Thêm danh mục mới
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Cấu trúc danh mục</h5>
            <p class="text-muted mb-0">Kéo và thả để sắp xếp hoặc thay đổi cấp danh mục.</p>
        </div>
        <div class="card-body">
            @if($categories->isEmpty())
                <p class="text-center">Chưa có danh mục nào. 
                    <a href="{{ route('admin.categories.create') }}">Tạo danh mục mới</a>.
                </p>
            @else
                {{-- Cấu trúc HTML của Nestable --}}
                <div class="dd" id="category-nestable">
                    <ol class="dd-list">
                        {{-- Gọi file partial đệ quy --}}
                        @include('admin.categories._item', ['items' => $categories])
                    </ol>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Cần jQuery (Nestable yêu cầu) --}}
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}} 
<script src="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js"></script>

<script>
$(function() {
    // 1. Khởi tạo Nestable
    $('#category-nestable').nestable({
        maxDepth: 3 // Giới hạn 3 cấp (cha -> con -> cháu), thay đổi tùy ý
    });

    // 2. Gán sự kiện 'change' khi kéo thả xong
    $('#category-nestable').on('change', function(e) {
        var list = e.length ? e : $(e.target);
        // Serialize() sẽ biến cấu trúc cây thành JSON
        var serializedData = list.nestable('serialize'); 
        
        // 3. Gửi AJAX lên route 'reorder'
        $.ajax({
            url: "{{ route('admin.categories.reorder') }}",
            type: "POST",
            data: {
                list: serializedData, // Gửi dữ liệu JSON lồng nhau
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                if(res.success) {
                    // Bạn có thể thêm thông báo (toast) ở đây
                    console.log(res.message); 
                } else {
                    console.error(res.message);
                    alert('Đã xảy ra lỗi khi cập nhật thứ tự.');
                }
            },
            error: function(xhr) {
                console.error('Lỗi máy chủ.');
                alert('Đã xảy ra lỗi máy chủ. Vui lòng thử lại.');
            }
        });
    });
});
</script>
@endsection