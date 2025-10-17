<?php

namespace App\Http\Controllers\Admin;

// QUAN TRỌNG NHẤT: Thêm dòng "use" này để kết nối với Controller cơ bản của Laravel
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class CustomerController extends Controller // Đảm bảo class này "extends Controller"
{
     use AuthorizesRequests;
    public function index(Request $request)
    {
        // Bây giờ, hàm authorize() sẽ được tìm thấy và hoạt động bình thường
        $this->authorize('user-list');

        $search = $request->input('search');
        $query = User::where('role', 'customer');

        $query->withCount('orders')->withSum('orders', 'total_price');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $customers = $query->orderByDesc('orders_sum_total_price')->paginate(10);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $this->authorize('user-list');

        if ($customer->role !== 'customer') {
            abort(404);
        }
        $orders = $customer->orders()->latest()->get();
        return view('admin.customers.show', compact('customer', 'orders'));
    }

    public function edit(User $customer)
    {
        $this->authorize('user-edit');

        if ($customer->role !== 'customer') {
            abort(404);
        }
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        $this->authorize('user-edit');

        if ($customer->role !== 'customer') {
            abort(404);
        }

        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $customer->update($request->only('fullname', 'phone', 'status'));

        return redirect()->route('admin.customers.index')->with('success', 'Cập nhật thông tin khách hàng thành công!');
    }
}
