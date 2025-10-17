@extends('admin.layouts.app')

@section('title', 'Hóa đơn - ' . $order->code)

@section('content')
<style>
    body {
        background: #f5f6fa;
    }
    .invoice-wrapper {
        max-width: 900px;
        margin: 20px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        padding: 40px 50px;
    }
    .invoice-header {
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .invoice-header img {
        height: 60px;
    }
    .invoice-status {
        font-weight: bold;
        text-transform: capitalize;
        border-radius: 6px;
        padding: 4px 10px;
        color: #fff;
    }
    .status-pending { background-color: #ffc107; }
    .status-confirmed { background-color: #0d6efd; }
    .status-shipping { background-color: #17a2b8; }
    .status-completed { background-color: #28a745; }
    .status-cancelled { background-color: #6c757d; }
    .status-failed { background-color: #dc3545; }

    table th, table td {
        vertical-align: middle !important;
    }
    .table thead {
        background: #007bff;
        color: white;
    }
    .summary {
        text-align: right;
        font-size: 18px;
        margin-top: 10px;
        font-weight: 600;
    }
    .note-section {
        margin-top: 30px;
        border-top: 1px dashed #ccc;
        padding-top: 20px;
    }
</style>

<div class="invoice-wrapper">
    {{-- Header --}}
    <div class="invoice-header">
        <div>
            <h3 class="fw-bold mb-1">SneakerUp</h3>
            <p class="text-muted mb-0">Website mua sắm giày SneakerUp</p>
        </div>
        <div class="text-end">
            <h4 class="text-primary mb-1">HÓA ĐƠN</h4>
            <p class="mb-0">Mã đơn: <strong>{{ $order->code }}</strong></p>
            <span class="invoice-status status-{{ $order->status }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>
    </div>

    {{-- Thông tin khách hàng và thanh toán --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <h5 class="fw-bold">👤 Thông tin người nhận</h5>
            <p class="mb-1">{{ $order->fullname }}</p>
            <p class="mb-1">{{ $order->address }}</p>
            <p class="mb-1">{{ $order->phone }}</p>
        </div>
        <div class="col-md-6">
            <h5 class="fw-bold">💳 Thanh toán & vận chuyển</h5>
            <p class="mb-1">Phương thức: {{ ucfirst($order->payment_method) }}</p>
            <p class="mb-1">Trạng thái thanh toán: 
                <span class="text-{{ $order->payment->status === 'paid' ? 'success' : ($order->payment->status === 'failed' ? 'danger' : 'secondary') }}">
                    {{ ucfirst($order->payment->status ?? '-') }}
                </span>
            </p>
            <p class="mb-0">Đơn vị vận chuyển: {{ $order->provider->name ?? '-' }}</p>
        </div>
    </div>

    {{-- Bảng sản phẩm --}}
    <h5 class="fw-bold mb-2">📦 Chi tiết đơn hàng</h5>
    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>#</th>
                <th>Sản phẩm</th>
                <th>Size</th>
                <th>Màu</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Tạm tính</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderDetails as $index => $d)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $d->product_name }}</td>
                <td>{{ $d->size_value }}</td>
                <td>{{ $d->color_name }}</td>
                <td>{{ $d->quantity }}</td>
                <td>{{ number_format($d->price) }} ₫</td>
                <td>{{ number_format($d->subtotal) }} ₫</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Tổng tiền --}}
    <div class="summary">
        Tổng cộng: <span class="text-danger">{{ number_format($order->total_price) }} ₫</span>
    </div>

    {{-- Hành động quản lý --}}
    <div class="mt-4">
        @php
            $current = $order->status;

            // Bản đồ quy tắc chuyển trạng thái hợp lệ
            $allowedTransitions = [
                'pending'   => ['confirmed', 'cancelled', 'failed'],
                'confirmed' => ['shipping', 'cancelled'],
                'shipping'  => ['completed', 'failed'],
                'completed' => [],
                'cancelled' => [],
                'failed'    => [],
            ];

            $nextStatuses = $allowedTransitions[$current] ?? [];
        @endphp

        <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="mt-4 d-inline-block">
            @csrf
            <label for="status" class="form-label fw-bold">Chuyển trạng thái:</label>
            <div class="input-group" style="max-width:300px;">
                <select name="status" id="status" class="form-select">
                    <option value="{{ $current }}" selected disabled>{{ ucfirst($current) }}</option>
                    @forelse($nextStatuses as $s)
                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                    @empty
                        <option disabled>Không thể chuyển tiếp</option>
                    @endforelse
                </select>
                <button class="btn btn-primary" @if(empty($nextStatuses)) disabled @endif>
                    Cập nhật
                </button>
            </div>
        </form>

        @if(session('error'))
            <div class="alert alert-danger mt-3">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif


        <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-outline-secondary ms-3">
            <i class="bi bi-file-earmark-pdf"></i> Tải PDF
        </a>
    </div>

    {{-- Ghi chú nội bộ --}}
    <div class="note-section">
        <h5 class="fw-bold">📝 Ghi chú</h5>
        <form method="POST" action="{{ route('admin.orders.addNote', $order) }}">
            @csrf
            <textarea name="note" class="form-control mb-2" rows="3" placeholder="Nhập ghi chú..."></textarea>
            <button class="btn btn-secondary">Thêm ghi chú</button>
        </form>
    </div>
</div>
@endsection
