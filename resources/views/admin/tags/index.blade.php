@extends('admin.layout')

@section('content')
<div class="container">
    <h2>Quản lý Tags sản phẩm</h2>

    <a href="{{ route('tags.create') }}" class="btn btn-success mb-3">+ Thêm Tag</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Tag</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tags as $tag)
            <tr>
                <td>{{ $tag->id }}</td>
                <td>{{ $tag->name }}</td>
                <td>
                    <a href="{{ route('tags.edit', $tag->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('tags.destroy', $tag->id) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection