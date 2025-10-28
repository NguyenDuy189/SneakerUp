<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Discount;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductController extends Controller
{
    // Danh sách sản phẩm
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $sort = $request->input('sort');

        $query = Product::with(['category', 'brand', 'tags', 'discount']);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($category) {
            $query->where('category_id', $category);
        }

        if ($sort == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort == 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort == 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort == 'name_desc') {
            $query->orderBy('name', 'desc');
        }

        $products = $query->paginate(10)->withQueryString();
        $categories = Category::all();
        

        // dd(Product::with('discount')->first()->final_price);
        $first = Product::with('discount')->first();
// dd([
//     'product_id' => $first->id,
//     'price' => $first->price,
//     'discount' => $first->discount,
//     'final_price' => $first->final_price,
// ]);
        return view('admin.products.index', compact('products', 'categories', 'search', 'category', 'sort'));
    }


    // Form thêm sản phẩm
    public function create()
    {
        $brands = Brand::all();
        $discounts = Discount::all();
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.products.create', compact('brands', 'categories', 'tags', 'discounts'));
    }

    // Lưu sản phẩm mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required',
            'brand_id' => 'required',
            'discount_id' => 'nullable',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            
        ]);

        $product = new Product($request->except('image'));

        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/products'), $filename);
            $product->image = $filename;
        }

        $product->quantity = $request->quantity;
        $product->status = $request->quantity > 0 ? 1 : 0; // 1: còn hàng, 0: hết hàng

        $product->save();
        
        $product->tags()->sync($request->tags ?? []);
        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    // Form sửa sản phẩm
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $brands = Brand::all();
        $discounts = Discount::all();
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'brands', 'categories', 'discounts'));
    }

    // Cập nhật sản phẩm
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required',
            'brand_id' => 'required',
            'discount_id' => 'nullable',
            // 'discount_id' => $request->discount_id,
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048'
        ]);

        $product->fill($request->except('image'));

        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/products'), $filename);
            $product->image = $filename;
        }

        $product->status = $request->quantity > 0 ? 1 : 0;
        $product->save();
        if ($request->has('tags')) {
            $product->tags()->sync($request->tags);
            } else {
            $product->tags()->sync([]);
        }

        $product->tags()->sync($request->tags ?? []);  
        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    // Xóa sản phẩm
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image && file_exists(public_path('uploads/products/'.$product->image))) {
            unlink(public_path('uploads/products/'.$product->image));
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }
    public function export()
    {
    return Excel::download(new ProductsExport, 'products.xlsx');
    }

// Nhập Excel
    public function import(Request $request)
    {
    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);

    Excel::import(new ProductsImport, $request->file('file'));

    return redirect()->back()->with('success', 'Nhập dữ liệu thành công!');
    }


    public function exportExcel()
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // --- Header ---
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Tên sản phẩm');
    $sheet->setCellValue('C1', 'Giá');
    $sheet->setCellValue('D1', 'Mô tả');
    $sheet->setCellValue('E1', 'Thương hiệu ID');
    $sheet->setCellValue('F1', 'Danh mục ID');
    $sheet->setCellValue('G1', 'Ngày tạo');

    // --- Dữ liệu Ex ---
    $row = 2;
    foreach (Product::all() as $product) {
        $sheet->setCellValue("A{$row}", $product->id);
        $sheet->setCellValue("B{$row}", $product->name);
        $sheet->setCellValue("C{$row}", $product->price);
        $sheet->setCellValue("D{$row}", $product->description);
        $sheet->setCellValue("E{$row}", $product->brand_id);
        $sheet->setCellValue("F{$row}", $product->category_id);
        $sheet->setCellValue("G{$row}", $product->created_at);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    $fileName = 'danh_sach_san_pham.xlsx';
    $filePath = storage_path($fileName);

    $writer->save($filePath);

    return response()->download($filePath)->deleteFileAfterSend(true);
}

    public function importExcel(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);

    $file = $request->file('file');
    $spreadsheet = IOFactory::load($file->getRealPath());
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    // Bỏ qua dòng đầu (header)
    unset($rows[0]);

    foreach ($rows as $row) {
        Product::updateOrCreate(
            ['id' => $row[0]],
            [
                'name' => $row[1],
                'price' => $row[2],
                'description' => $row[3],
                'brand_id' => $row[4],
                'category_id' => $row[5],
                'created_at' => $row[6] ?? now(),
            ]
        );
    }

    return back()->with('success', 'Nhập dữ liệu sản phẩm thành công!');
}

//Bật / tắt hiển thị sản phẩm nổi bật
    public function toggleFeatured($id)
{
    $product = Product::findOrFail($id);
    $product->is_featured = !$product->is_featured;
    $product->save();

    return response()->json([
        'status' => 'success',
        'is_featured' => $product->is_featured
    ]);
}
}