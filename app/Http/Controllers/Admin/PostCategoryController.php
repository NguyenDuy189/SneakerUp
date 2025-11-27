<?php

namespace App\Http\Controllers\Admin; // Đảm bảo đúng namespace

use App\Http\Controllers\Controller; // Sửa ở đây
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Thêm dòng này

class PostCategoryController extends Controller
{
    use AuthorizesRequests; // Thêm dòng này

    public function index()
    {
        // $this->authorize('post-category-list'); // Sẽ thêm ở phần phân quyền
        $categories = PostCategory::paginate(10);
        return view('admin.post_categories.index', compact('categories'));
    }

    public function create()
    {
        // $this->authorize('post-category-create');
        return view('admin.post_categories.create');
    }

    public function store(Request $request)
    {
        // $this->authorize('post-category-create');
        $request->validate([
            'name' => 'required|string|max:255|unique:post_categories',
        ]);

        PostCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.post-categories.index')->with('success', 'Tạo danh mục thành công.');
    }

    public function edit(PostCategory $postCategory)
    {
        // $this->authorize('post-category-edit');
        return view('admin.post_categories.edit', compact('postCategory'));
    }

    public function update(Request $request, PostCategory $postCategory)
    {
        // $this->authorize('post-category-edit');
        $request->validate([
            'name' => 'required|string|max:255|unique:post_categories,name,' . $postCategory->id,
        ]);

        $postCategory->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.post-categories.index')->with('success', 'Cập nhật danh mục thành công.');
    }

    public function destroy(PostCategory $postCategory)
    {
        // $this->authorize('post-category-delete');
        // Cần kiểm tra xem danh mục có bài viết nào không trước khi xóa
        // if ($postCategory->posts()->count() > 0) {
        //     return back()->with('error', 'Không thể xóa danh mục này vì còn bài viết.');
        // }
        $postCategory->delete();
        return redirect()->route('admin.post-categories.index')->with('success', 'Xóa danh mục thành công.');
    }
}
