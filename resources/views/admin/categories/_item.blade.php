{{-- File này được gọi bởi index.blade.php --}}

@foreach($items as $item)
    <li class="dd-item" data-id="{{ $item->id }}">
        {{-- Phần tay cầm để kéo --}}
        <div class="dd-handle">
            {{ $item->name }}
        </div>
        
        {{-- Phần thông tin thêm và nút bấm --}}
        <div class="dd-content">
            @if($item->thumbnail)
                <img src="{{ $item->thumbnail }}" alt="{{ $item->name }}" class="item-thumbnail" style="width: 150px">
            @else
                <img src="https://via.placeholder.com/40x40?text=N/A" alt="No image" class="item-thumbnail">
            @endif

            <span class="badge {{ $item->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                {{ $item->status_label }}
            </span>
            <span class="badge bg-info ms-2">
                {{ $item->product_count }} sản phẩm
            </span>

            {{-- Các nút hành động --}}
            <div class="dd-actions">
                <a href="{{ route('admin.categories.edit', $item) }}" class="btn btn-sm btn-warning" title="Sửa">
                    <i class="fas fa-edit"></i>
                </a>
                
                {{-- Form xóa (Dùng JS để confirm) --}}
                <form action="{{ route('admin.categories.destroy', $item) }}" method="POST" class="d-inline" 
                    onsubmit="return confirm('Bạn có chắc muốn xóa danh mục [{{ $item->name }}]?\nLƯU Ý: Không thể xóa nếu có sản phẩm hoặc danh mục con!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- Gọi đệ quy nếu có con --}}
        @if($item->children->isNotEmpty())
            <ol class="dd-list">
                @include('admin.categories._item', ['items' => $item->children])
            </ol>
        @endif
    </li>
@endforeach