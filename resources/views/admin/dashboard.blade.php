@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-2"><i class="fa-solid fa-box text-primary me-2"></i> Tổng đơn hàng</h5>
                    <h3 class="mb-0">128</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-2"><i class="fa-solid fa-user text-success me-2"></i> Khách hàng</h5>
                    <h3 class="mb-0">72</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-2"><i class="fa-solid fa-dollar-sign text-warning me-2"></i> Doanh thu</h5>
                    <h3 class="mb-0">45,800,000₫</h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
