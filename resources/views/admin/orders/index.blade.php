@extends('admin.layouts.app')

@section('title', 'Quản lý đơn hàng')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-dark">
            <i class="bi bi-bag-check"></i> Quản lý đơn hàng
        </h2>
        <div>
            <a href="{{ route('admin.orders.export.csv') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-filetype-csv"></i> Xuất CSV
            </a>
            <a href="{{ route('admin.orders.export.excel') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Xuất Excel
            </a>
        </div>
    </div>

    {{-- Hiển thị thông báo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Bộ lọc --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                placeholder="Tìm mã đơn, tên KH, SĐT...">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- Trạng thái --</option>
                @foreach ([
                    'pending'=>'Chờ xử lý',
                    'confirmed'=>'Đã xác nhận',
                    'shipping'=>'Đang giao hàng',
                    'completed'=>'Hoàn thành',
                    'cancelled'=>'Đã huỷ',
                    'failed'=>'Thất bại',
                    'returned'=>'Trả hàng'
                ] as $key => $label)
                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="payment" class="form-select">
                <option value="">-- Thanh toán --</option>
                <option value="cod" {{ request('payment') == 'cod' ? 'selected' : '' }}>COD</option>
                <option value="banking" {{ request('payment') == 'banking' ? 'selected' : '' }}>Chuyển khoản</option>
                <option value="momo" {{ request('payment') == 'momo' ? 'selected' : '' }}>Momo</option>
                <option value="vnpay" {{ request('payment') == 'vnpay' ? 'selected' : '' }}>VNPAY</option>
            </select>
        </div>
        <div class="col-md-3 text-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Lọc
            </button>
        </div>
    </form>

    {{-- Bảng dữ liệu --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Mã đơn hàng</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="text-center">{{ $order->id }}</td>
                            <td class="fw-semibold text-primary">{{ $order->code }}</td>
                            <td>{{ $order->fullname ?? ($order->user->fullname ?? 'N/A') }}</td>
                            <td>{{ number_format($order->total_price, 0, ',', '.') }} đ</td>
                            <td>{{ strtoupper($order->payment_method ?? '---') }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status_color }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                
                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                    class="btn btn-outline-info btn-sm ms-1">
                                        <i class="bi bi-eye">Chi tiết</i>
                                </a>
                                @switch($order->status)
                                    @case('pending')
                                        <button class="btn btn-success btn-sm js-ajax-confirm"
                                            data-id="{{ $order->id }}" data-status="confirmed">
                                            <i class="bi bi-check2-circle"></i> Xác nhận
                                        </button>
                                        @break

                                    @case('confirmed')
                                        <button class="btn btn-primary btn-sm js-ajax-confirm"
                                            data-id="{{ $order->id }}" data-status="shipping">
                                            <i class="bi bi-truck"></i> Giao hàng
                                        </button>
                                        @break

                                    @case('shipping')
                                        <button class="btn btn-success btn-sm js-ajax-confirm"
                                            data-id="{{ $order->id }}" data-status="completed">
                                            <i class="bi bi-check2"></i> Hoàn thành
                                        </button>
                                        @break
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-3 text-muted">
                                <i class="bi bi-inbox"></i> Không có đơn hàng nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Phân trang --}}
    <div class="mt-3">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- AJAX cập nhật trạng thái --}}
<script>
document.addEventListener('click', e => {
    const btn = e.target.closest('.js-ajax-confirm');
    if (!btn) return;
    const id = btn.dataset.id;
    const status = btn.dataset.status;

    if (confirm(`Bạn có chắc muốn chuyển đơn #${id} sang trạng thái ${status}?`)) {
        fetch(`/admin/orders/${id}/ajax-update-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) location.reload();
        })
        .catch(err => alert('Lỗi: ' + err.message));
    }
});
</script>
@endsection
