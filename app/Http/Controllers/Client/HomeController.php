<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ.
     */
    public function index()
    {
        // Tạm thời chúng ta chỉ trả về view
        // Bước tiếp theo, chúng ta sẽ lấy CSDL sản phẩm sale ở đây

        return view('client.home');
    }
}
