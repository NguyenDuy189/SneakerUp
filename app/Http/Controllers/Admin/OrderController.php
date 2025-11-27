<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order; // Sử dụng Model Order
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(Order $order)
{
    // === PHẦN NÂNG CẤP ===
    // Dùng load() để tải kèm mối quan hệ 'details' mà chúng ta vừa tạo.
    // Dữ liệu chi tiết sẽ được gán vào thuộc tính $order->details
    $order->load('details');

    return view('admin.orders.show', compact('order'));
}
}
