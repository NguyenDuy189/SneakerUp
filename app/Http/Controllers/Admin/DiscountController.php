<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discount;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::paginate(10);
        return view('admin.discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('admin.discounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:discounts,code',
            'value' => 'required|numeric|min:1',
        ]);

        Discount::create($request->all());

        return redirect()->route('admin.discounts.index')->with('success', 'Tạo mã giảm giá thành công!');
    }

    public function edit($id)
    {
        $discount = Discount::findOrFail($id);
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(Request $request, $id)
    {
        $discount = Discount::findOrFail($id);
        $discount->update($request->all());
        return redirect()->route('admin.discounts.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();
        return back()->with('success', 'Xoá thành công!');
    }
}
