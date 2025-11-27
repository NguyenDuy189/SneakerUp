<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Dùng để lấy thông tin user đã đăng nhập
use App\Models\Order; // Dùng để lấy lịch sử đơn hàng
use App\Models\UserAddress;
use App\Models\OrderDetail;
class AccountController extends Controller
{
    /**
     * Task: Hiển thị form "Thông tin người dùng"
     */
    public function showProfileForm()
    {
        $user = Auth::user(); // Lấy user đang đăng nhập
        return view('client.account.profile', compact('user'));
    }

    /**
     * Task: "Thay đổi thông tin"
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            // Đảm bảo email là duy nhất, TRỪ email hiện tại của chính user này
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'fullname' => $request->fullname,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);

        return redirect()->route('client.profile.show')->with('success', 'Cập nhật thông tin thành công!');
    }

    /**
     * Task: "Xem lịch sử mua hàng"
     */
    public function showOrders()
    {
        $userId = Auth::id(); // Lấy ID của user đang đăng nhập

        // Chỉ lấy các đơn hàng của user này
        $orders = Order::where('user_id', $userId)
                        ->latest() // Sắp xếp mới nhất lên đầu
                        ->paginate(10);

        return view('client.account.orders', compact('orders'));
    }
    // === BẮT ĐẦU CÁC HÀM XỬ LÝ SỔ ĐỊA CHỈ ===

    /**
     * Task 3: Hiển thị trang "Danh sách địa chỉ"
     */
    public function addressIndex()
    {
        $user = Auth::user();
        // Lấy tất cả địa chỉ của user này, sắp xếp Mặc định lên đầu
        $addresses = $user->addresses()->orderByDesc('is_default')->get();

        return view('client.account.address.index', compact('addresses'));
    }

    /**
     * Task 3: Hiển thị form "Thêm địa chỉ mới"
     */
    public function addressCreate()
    {
        return view('client.account.address.create');
    }

    /**
     * Task 3: Lưu địa chỉ mới vào CSDL
     */
    public function addressStore(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        // Logic "Mặc định": Nếu user tick vào ô "is_default"
        if ($request->has('is_default')) {
            // Hủy tất cả "mặc định" cũ của user này
            $user->addresses()->update(['is_default' => false]);
        }

        // Tạo địa chỉ mới
        $user->addresses()->create([
            'fullname' => $request->fullname,
            'phone' => $request->phone,
            'address' => $request->address,
            // Nếu 'is_default' được tick, lưu là true, ngược lại là false
            'is_default' => $request->has('is_default'),
        ]);

        return redirect()->route('client.address.index')->with('success', 'Thêm địa chỉ mới thành công!');
    }

    /**
     * Task 3: Hiển thị form "Sửa địa chỉ"
     */
    public function addressEdit(UserAddress $address)
    {
        // QUAN TRỌNG: Kiểm tra xem địa chỉ này có đúng là của user đang đăng nhập không
        if ($address->user_id !== Auth::id()) {
            abort(403); // Báo lỗi "Không có quyền"
        }

        return view('client.account.address.edit', compact('address'));
    }

    /**
     * Task 3: Cập nhật địa chỉ
     */
    public function addressUpdate(Request $request, UserAddress $address)
    {
        // Kiểm tra quyền sở hữu
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        // Logic "Mặc định"
        if ($request->has('is_default')) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address->update([
            'fullname' => $request->fullname,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_default' => $request->has('is_default'),
        ]);

        return redirect()->route('client.address.index')->with('success', 'Cập nhật địa chỉ thành công!');
    }

    /**
     * Task 3: Xóa địa chỉ
     */
    public function addressDestroy(UserAddress $address)
    {
        // Kiểm tra quyền sở hữu
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return redirect()->route('client.address.index')->with('success', 'Xóa địa chỉ thành công.');
    }

    /**
     * Task 3: "Thiết lập làm Mặc định"
     */
    public function addressSetDefault(UserAddress $address)
    {
        // Kiểm tra quyền sở hữu
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $user = Auth::user();

        // 1. Hủy tất cả "mặc định" cũ
        $user->addresses()->update(['is_default' => false]);

        // 2. Đặt địa chỉ này làm mặc định mới
        $address->update(['is_default' => true]);

        return redirect()->route('client.address.index')->with('success', 'Đặt địa chỉ mặc định thành công!');
    }
    /**
 * Task 4: Hiển thị trang "Chi Tiết Đơn Hàng"
 */
public function showOrderDetail(Order $order)
{
    // 1. Kiểm tra xem đơn hàng này có đúng là của user đang đăng nhập không
    if ($order->user_id !== Auth::id()) {
        abort(403); // Báo lỗi "Không có quyền"
    }

    // 2. Tải các "chi tiết" (danh sách sản phẩm) của đơn hàng này
    //    Đây là hàm 'details()' mà chúng ta đã thêm vào Model 'Order'
    $order->load('details');

    // 3. Gửi dữ liệu ra view
    return view('client.account.order_detail', compact('order'));
}
}
