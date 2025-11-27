<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReviewController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        // $this->authorize('review-list');

        $query = Review::with(['user', 'product']);

        // SỬA LẠI LOGIC LỌC: Dùng 'visible' và 'hidden'
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $reviews = $query->latest()->paginate(15);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function edit(Review $review)
    {
        // $this->authorize('review-edit');
        $review->load(['user', 'product']);
        return view('admin.reviews.edit', compact('review'));
    }

  public function update(Request $request, Review $review)
    {
        // $this->authorize('review-edit');

        $request->validate([
            // Các trường 'rating' và 'comment' đã bị vô hiệu hóa (disabled)
            // nên chúng sẽ không được gửi lên, không cần validate

            'status' => 'required|in:visible,hidden',
            'admin_reply' => 'nullable|string|max:5000', // Thêm validate cho trường mới
        ]);

        // Chuẩn bị dữ liệu để cập nhật
        $dataToUpdate = [
            'status' => $request->status,
            'admin_reply' => $request->admin_reply,
        ];

        // Logic quan trọng:
        // Chỉ lưu ngày phản hồi (replied_at) LẦN ĐẦU TIÊN
        // khi admin nhập phản hồi và trước đó chưa có phản hồi.
        if ($request->filled('admin_reply') && is_null($review->admin_reply)) {
            $dataToUpdate['replied_at'] = now(); // now() = lấy ngày giờ hiện tại
        }

        $review->update($dataToUpdate);

        return redirect()->route('admin.reviews.index')->with('success', 'Cập nhật đánh giá thành công.');
    }

    public function destroy(Review $review)
    {
        // $this->authorize('review-delete');
        $review->delete();
        return redirect()->route('admin.reviews.index')->with('success', 'Xóa đánh giá thành công.');
    }
}
