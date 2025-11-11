<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Import;
use App\Models\ImportDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    // 📦 Danh sách sản phẩm trong kho
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['brand', 'category', 'variants']);

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $products = $query->paginate(10);
        $lowStockProducts = $query->get()->filter(function ($p) {
            return ($p->variants->sum('stock') + $p->quantity) < 5;
        });
        return view('admin.warehouse.index', compact('products', 'lowStockProducts'));
    }

    // ➕ Form nhập kho
    public function showImportForm($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        return view('admin.warehouse.form', ['product' => $product, 'action' => 'import']);
    }

    // 💾 Lưu nhập kho
    public function storeImport(Request $request, $id)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0'
        ]);

        DB::transaction(function () use ($request, $id) {
            $variant = ProductVariant::findOrFail($request->variant_id);
            $variant->stock += $request->quantity;
            $variant->save();

            $product = Product::find($id);
            $product->quantity += $request->quantity;
            $product->save();

            $import = Import::create([
                'supplier_id' => 1, // có thể thay bằng supplier thật
                'total_price' => $request->price * $request->quantity
            ]);

            ImportDetail::create([
                'import_id' => $import->id,
                'variant_id' => $variant->id,
                'quantity' => $request->quantity,
                'price' => $request->price
            ]);
        });

        return redirect()->route('admin.warehouse.index')->with('success', 'Nhập kho thành công!');
    }

    // ➖ Form xuất kho
    public function showExportForm($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        return view('admin.warehouse.form', ['product' => $product, 'action' => 'export']);
    }

    // 💾 Lưu xuất kho
    public function storeExport(Request $request, $id)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        DB::transaction(function () use ($request, $id) {
            $variant = ProductVariant::findOrFail($request->variant_id);
            if ($variant->stock < $request->quantity) {
                abort(400, 'Số lượng xuất vượt quá tồn kho');
            }

            $variant->stock -= $request->quantity;
            $variant->save();

            $product = Product::find($id);
            $product->quantity -= $request->quantity;
            $product->save();
        });

        return redirect()->route('admin.warehouse.index')->with('success', 'Xuất kho thành công!');
    }
}
