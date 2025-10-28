<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductVariantController extends Controller
{
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        $variants = $product->variants;
        return view('admin.variants.index', compact('product', 'variants'));
    }

    public function create($productId)
    {
        $product = Product::findOrFail($productId);
        return view('admin.variants.create', compact('product'));
    }

    public function store(Request $request, $productId)
    {
        $request->validate([
            'size' => 'required|string',
            'color' => 'required|string',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('variants', 'public');
        }

        ProductVariant::create([
            'product_id' => $productId,
            'size' => $request->size,
            'color' => $request->color,
            'stock' => $request->stock,
            'price' => $request->price,
            'image' => $imagePath,
            'sku'   => $request->sku ?? Str::upper(Str::random(8)),
        ]);

        return redirect()->route('variants.index', $productId)->with('success', 'Thêm biến thể thành công!');
    }

    public function destroy($id)
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->delete();
        return back()->with('success', 'Xóa biến thể thành công!');
    }
}
