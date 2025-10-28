@extends('admin.layout')

@section('content')
<div class="container">
    <div class="form-group">
     <label>Tags:</label>
     <select name="tags[]" class="form-control" multiple>
        @foreach($tags as $tag)
            <option value="{{ $tag->id }}" 
                {{ isset($product) && $product->tags->contains($tag->id) ? 'selected' : '' }}>
                {{ $tag->name }}
            </option>
        @endforeach
     </select>
    </div>
    <h2>{{ isset($tag) ? 'Sửa Tag' : 'Thêm Tag' }}</h2>

    <form action="{{ isset($tag) ? route('tags.update', $tag->id) : route('tags.store') }}" method="POST">
        @csrf
        @if(isset($tag))
            @method('PUT')
        @endif

        <div class="form-group mb-3">
            <label>Tên Tag:</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $tag->name ?? '') }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="{{ route('tags.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection