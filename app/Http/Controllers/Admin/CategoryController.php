<?php

// app/Http/Controllers/Admin/CategoryController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        // lấy tất cả để hiển thị cây cha-con
        $categories = Category::with('children')->whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        // handle image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('categories/banners', 'public');
        }

        Category::create($data);
        return redirect()->route('admin.categories.index')->with('success','Tạo danh mục thành công.');
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')->where('id','!=',$category->id)->get();
        return view('admin.categories.edit', compact('category','parents'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        if ($request->hasFile('image')) {
            // xóa file cũ
            if ($category->image) Storage::disk('public')->delete($category->image);
            $data['image'] = $request->file('image')->store('categories', 'public');
        }
        if ($request->hasFile('banner')) {
            if ($category->banner) Storage::disk('public')->delete($category->banner);
            $data['banner'] = $request->file('banner')->store('categories/banners', 'public');
        }

        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success','Cập nhật danh mục thành công.');
    }

    public function destroy(Category $category)
    {
        // xóa ảnh nếu có
        if ($category->image) Storage::disk('public')->delete($category->image);
        if ($category->banner) Storage::disk('public')->delete($category->banner);

        // nếu có children: bạn có thể chọn xóa đệ quy hoặc set parent_id = null.
        // Ở đây ta set parent_id của children = null trước khi xóa
        $category->children()->update(['parent_id' => null]);
        $category->products()->detach();
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success','Xóa danh mục thành công.');
    }
}
