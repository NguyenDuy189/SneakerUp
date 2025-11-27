{{--
    BƯỚC QUAN TRỌNG:
    Chúng ta kế thừa layout 'client' (Header/Footer xanh)
--}}
@extends('layouts.client')

{{-- Đặt Tiêu đề cho trang này --}}
@section('title', 'Trang Chủ - SneakerUp')

{{--
    Nội dung của trang chủ sẽ được đặt vào đây
    và "hút" vào @yield('content') của file layout
--}}
@section('content')

    <section class="hero-banner" style="background-image: url('{{-- Thêm link ảnh banner của bạn vào đây --}}');">
        <div class="banner-content">
            <h1>New Collection</h1>
            <h2>SUMMER SALE</h2>
            <a href="#" class="btn btn-primary">ORDER NOW</a>
        </div>
    </section>

    <section class="product-section">
        <div class="section-header">
            <h2>SẢN PHẨM SALE</h2>
            <a href="#" class="view-all">Xem thêm</a>
        </div>

        <div class="product-grid">
            <div class="product-card">Product 1</div>
            <div class="product-card">Product 2</div>
            <div class="product-card">Product 3</div>
            <div class="product-card">Product 4</div>
        </div>
    </section>

    <section class="product-section">
        <div class="section-header">
            <h2>SẢN PHẨM CHẠY BỘ</h2>
            <a href="#" class="view-all">Xem thêm</a>
        </div>

        <div class="product-grid">
            <div class="product-card">Product 5</div>
            <div class="product-card">Product 6</div>
            <div class="product-card">Product 7</div>
            <div class="product-card">Product 8</div>
        </div>
    </section>

    @endsection
