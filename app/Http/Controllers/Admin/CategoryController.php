<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\Admin\SaveCategoryRequest; // Dùng Request
use App\Services\ImageUploadService;             // Dùng Service
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    protected $imageService;

    // Tiêm (Inject) Service vào Controller
    public function __construct(ImageUploadService $imageService)
    {
        $this->imageService = $imageService;
    }

    // Trang danh sách
    public function index()
    {
        $categories = Category::with(['children'])
            ->withCount('products')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
        return view('admin.categories.index', compact('categories'));
    }

    // Hiển thị Form (Tạo/Sửa)
    public function form(?Category $category = null)
    {
        $category = $category ?? new Category(['status' => 'active']);
        $query = Category::orderBy('sort_order');
        
        // Khi edit, không cho chọn chính nó hoặc con của nó làm cha
        $excludeIds = [];
        if ($category->exists) {
           $excludeIds = $this->getCategoryTreeIds($category);
        }
        $parents = $query->whereNotIn('id', $excludeIds)->get();

        return view('admin.categories.form', compact('category', 'parents'));
    }
    
    // Helper: Lấy ID của chính nó và tất cả con cháu (đệ quy)
    private function getCategoryTreeIds(Category $category): array
    {
        $ids = [$category->id];
        foreach ($category->children as $child) {
            $ids = array_merge($ids, $this->getCategoryTreeIds($child));
        }
        return $ids;
    }


    public function create()
    {
        return $this->form();
    }

    public function edit(Category $category)
    {
        return $this->form($category);
    }

    // Xử lý Store (Dùng SaveCategoryRequest)
    public function store(SaveCategoryRequest $request)
    {
        return $this->save($request, new Category());
    }

    // Xử lý Update (Dùng SaveCategoryRequest)
    public function update(SaveCategoryRequest $request, Category $category)
    {
        return $this->save($request, $category);
    }

    // Logic LƯU (Create/Update) - Đã chính xác
    private function save(SaveCategoryRequest $request, Category $category)
    {
        // 1. Lấy dữ liệu đã validate, *ngoại trừ* file
        $data = $request->safe()->except('thumbnail_file');
        $isNew = !$category->exists;

        DB::transaction(function () use ($category, $request, $data, $isNew) {
            
            // 2. Xử lý upload thumbnail (nếu có file mới)
            if ($request->hasFile('thumbnail_file')) {
                $data['thumbnail'] = $this->imageService->handleUpload(
                    $request->file('thumbnail_file'),
                    'categories',
                    $category->thumbnail // Truyền ảnh cũ để xóa
                );
            }
            
            // 3. Tạo Slug duy nhất
            $data['slug'] = $this->generateUniqueSlug($data['name'], $category->id);

            // 4. Tự động gán sort_order nếu là tạo mới
            if ($isNew) {
                $maxOrder = Category::where('parent_id', $data['parent_id'] ?? null)->max('sort_order');
                $category->sort_order = $maxOrder + 1;
            }

            // 5. Fill và Save
            $category->fill($data);
            $category->save();
        });

        $message = $isNew ? 'Tạo danh mục thành công!' : 'Cập nhật danh mục thành công!';
        return redirect()->route('admin.categories.index')->with('success', $message);
    }
    
    // Hàm tạo slug duy nhất (Đã chính xác)
    private function generateUniqueSlug($name, $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;
        
        $query = Category::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        
        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count++;
            $query = Category::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }
        return $slug;
    }

    // Xóa (Đã chính xác)
    public function destroy(Category $category, Request $request)
    {
        if ($category->products()->exists()) {
            $message = 'Không thể xóa danh mục đang có sản phẩm!';
            return $this->jsonOrRedirect($request, false, $message);
        }

        if ($category->children()->exists()) {
            $message = 'Không thể xóa danh mục đang có danh mục con!';
            return $this->jsonOrRedirect($request, false, $message);
        }

        if ($category->thumbnail) {
            $diskPath = Str::replace('/storage/', '', $category->thumbnail);
            if (Storage::disk('public')->exists($diskPath)) {
                Storage::disk('public')->delete($diskPath);
            }
        }

        $category->delete();
        
        return $this->jsonOrRedirect($request, true, 'Đã xóa danh mục thành công!');
    }
    
    // Helper JSON/Redirect (Đã chính xác)
    private function jsonOrRedirect(Request $request, bool $success, string $message)
    {
        if ($request->ajax()) {
            return response()->json(['success' => $success, 'message' => $message]);
        }
        $status = $success ? 'success' : 'warning';
        return back()->with($status, $message);
    }

    // Reorder (Đã chính xác)
    public function reorder(Request $request)
    {
        $list = $request->input('list', []);

        if (empty($list)) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu rỗng'], 400);
        }

        try {
            DB::transaction(function () use ($list) {
                $this->updateOrder($list);
            });
            return response()->json(['success' => true, 'message' => 'Đã cập nhật thứ tự danh mục!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // UpdateOrder (Đã chính xác)
    protected function updateOrder(array $items, $parentId = null)
    {
        foreach ($items as $index => $item) {
            Category::where('id', $item['id'])->update([
                'sort_order' => $index + 1,
                'parent_id' => $parentId
            ]);

            if (isset($item['children']) && !empty($item['children'])) {
                $this->updateOrder($item['children'], $item['id']);
            }
        }
    }
}