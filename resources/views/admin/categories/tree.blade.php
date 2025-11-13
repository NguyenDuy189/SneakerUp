@extends('admin.layouts.app')

@section('title', 'Danh mục - Sơ đồ cây')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-3">
        <h4>Danh mục sản phẩm</h4>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-success">Thêm danh mục mới</a>
    </div>

    <ul class="list-group">
        @foreach($categories as $category)
            @include('admin.categories.partials.category-node', ['category' => $category])
        @endforeach
    </ul>
</div>

{{-- Optional: JS để collapse tree --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-children').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const target = document.querySelector(btn.dataset.target);
            if (target) {
                target.classList.toggle('d-none');
                btn.textContent = target.classList.contains('d-none') ? '+' : '-';
            }
        });
    });
});
</script>
@endsection
