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
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            // SỬA LẠI LOGIC VALIDATE: Dùng 'visible' và 'hidden'
            'status' => 'required|in:visible,hidden',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.reviews.index')->with('success', 'Cập nhật đánh giá thành công.');
    }

    public function destroy(Review $review)
    {
        // $this->authorize('review-delete');
        $review->delete();
        return redirect()->route('admin.reviews.index')->with('success', 'Xóa đánh giá thành công.');
    }
}
