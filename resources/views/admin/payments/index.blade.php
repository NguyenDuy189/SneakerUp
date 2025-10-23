@extends('admin.layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">💳 Quản lý thanh toán</h2>

    {{-- Lọc & tìm kiếm --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form action="{{ route('admin.payments.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                placeholder="🔍 Tìm mã đơn, khách hàng, SĐT, phương thức..."
                class="form-control" style="width: 300px;">

            <select name="status" class="form-select" style="width: 180px;">
                <option value="">-- Tất cả trạng thái --</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Lọc
            </button>
        </form>

        @if(request()->has('search') || request()->has('status'))
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Xóa lọc
            </a>
        @endif
    </div>

    {{-- Bảng danh sách thanh toán --}}
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Phương thức</th>
                    <th>Trạng thái</th>
                    <th>Tổng + ship</th>
                    <th>Điểm thưởng</th>
                    <th>Ngày thanh toán</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>
                            @if($payment->order)
                                <a href="{{ route('admin.orders.show', $payment->order) }}">#{{ $payment->order->code }}</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $payment->order->user->fullname ?? '-' }}</td>
                        <td>{{ $payment->method_label }}</td>
                        <td>
                            <span class="badge bg-{{ $statusColors[$payment->status] ?? 'secondary' }}">
                                {{ $statuses[$payment->status] ?? ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td>
                            @if($payment->order)
                                {{ number_format($payment->order->total_price + $payment->order->shipping_fee,0,',','.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $payment->order->user->points ?? 0 }}</td>
                        <td>{{ $payment->paid_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> Chi tiết
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Không có thanh toán nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Phân trang --}}
    <div class="mt-3">
        {{ $payments->withQueryString()->links() }}
    </div>
</div>
@endsection
