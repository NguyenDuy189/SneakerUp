<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage; // Import Storage để xử lý file

class PostController extends Controller
{
    use AuthorizesRequests;

   public function index(Request $request)
    {
        // $this->authorize('post-list');

        // 1. Bắt đầu một câu truy vấn, luôn lấy kèm category
        $query = Post::with('category');

        // 2. Kiểm tra xem có từ khóa tìm kiếm được gửi lên không
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;

            // 3. Thêm điều kiện "where" để tìm kiếm theo 'title' (tiêu đề)
            // Dùng 'like' và '%' để tìm kiếm tương đối
            $query->where('title', 'like', '%' . $searchTerm . '%');
        }

        // 4. Sắp xếp và phân trang
        // $posts = $query->latest()->paginate(10);

        // 5. QUAN TRỌNG: Thêm ->appends() để giữ lại từ khóa tìm kiếm
        // khi bạn bấm sang Trang 2, Trang 3...
        $posts = $query->latest()->paginate(10)->appends($request->query());

        // 6. Trả về view với kết quả
        return view('admin.posts.index', compact('posts'));
    }
    public function create()
    {
        // $this->authorize('post-create');

        // Lấy tất cả danh mục đang 'active' để hiển thị trong <select>
        $categories = PostCategory::where('status', true)->get();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // $this->authorize('post-create');

        $request->validate([
            'title' => 'required|string|max:255|unique:posts',
            'post_category_id' => 'required|exists:post_categories,id',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB max
            'status' => 'required|in:draft,published',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Lưu ảnh vào thư mục 'public/posts'
            // 'posts' là tên thư mục bạn tự đặt
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        Post::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'post_category_id' => $request->post_category_id,
            'content' => $request->content,
            'status' => $request->status,
            'image' => $imagePath, // Lưu đường dẫn ảnh vào CSDL
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Tạo bài viết thành công.');
    }

    public function edit(Post $post)
    {
        // $this->authorize('post-edit');

        // Lấy danh mục để hiển thị <select>
        $categories = PostCategory::where('status', true)->get();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        // $this->authorize('post-edit');

        $request->validate([
            'title' => 'required|string|max:255|unique:posts,title,' . $post->id,
            'post_category_id' => 'required|exists:post_categories,id',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $imagePath = $post->image; // Giữ lại ảnh cũ làm mặc định
        if ($request->hasFile('image')) {
            // 1. Xóa ảnh cũ (nếu có)
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            // 2. Lưu ảnh mới
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $post->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'post_category_id' => $request->post_category_id,
            'content' => $request->content,
            'status' => $request->status,
            'image' => $imagePath, // Cập nhật đường dẫn ảnh
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công.');
    }

    public function destroy(Post $post)
    {
        // $this->authorize('post-delete');

        // Xóa ảnh bìa khỏi storage
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        // Xóa bài viết khỏi CSDL
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Xóa bài viết thành công.');
    }
}
