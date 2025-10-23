@extends('admin.layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-uppercase mb-4">💳 Chi tiết thanh toán #{{ $payment->id }}</h2>

    {{-- Flash --}}
    @foreach(['success','error','info'] as $type)
        @if(session($type))
            <div class="alert alert-{{ $type==='error'?'danger':$type }}">{{ session($type) }}</div>
        @endif
    @endforeach

    <div class="row g-4">
        {{-- Thông tin đơn hàng & thanh toán --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light fw-semibold">Thông tin thanh toán</div>
                <div class="card-body">
                    <p><strong>Đơn hàng:</strong>
                        @if($payment->order)
                            <a href="{{ route('admin.orders.show', $payment->order) }}">#{{ $payment->order->code }}</a>
                        @else
                            <span class="text-muted">Không tìm thấy</span>
                        @endif
                    </p>
                    <p><strong>Phương thức:</strong> {{ $payment->method_label }}</p>
                    <p><strong>Trạng thái:</strong>
                        <span class="badge bg-{{ $statusColors[$payment->status] ?? 'secondary' }}">
                            {{ $statuses[$payment->status] ?? ucfirst($payment->status) }}
                        </span>
                    </p>
                    <p><strong>Ngày thanh toán:</strong> {{ $payment->paid_at?->format('d/m/Y H:i') ?? 'Chưa có' }}</p>
                    <p><strong>Mã hoàn tiền:</strong> {{ $payment->refund_reference ?? '-' }}</p>
                    <p><strong>Nhân viên xác nhận:</strong> {{ $payment->confirmer?->fullname ?? 'Chưa xác nhận' }}</p>
                </div>
            </div>

            {{-- Cập nhật trạng thái --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light fw-semibold">🔁 Cập nhật trạng thái</div>
                <div class="card-body">
                    <form action="{{ route('admin.payments.updateStatus', $payment) }}" method="POST" class="row g-3 align-items-center">
                        @csrf
                        @method('PATCH')

                        <div class="col-auto">
                            <select name="status" class="form-select">
                                <option value="" selected disabled>-- Chọn trạng thái mới --</option>
                                @forelse($nextStatuses as $status)
                                    <option value="{{ $status }}">{{ $statuses[$status] }}</option>
                                @empty
                                    <option disabled>Không thể chuyển tiếp</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="col-auto">
                            <button type="submit" class="btn btn-success" @if(empty($nextStatuses)) disabled @endif>
                                <i class="bi bi-arrow-repeat me-1"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Thông tin khách hàng và chi tiết đơn hàng --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light fw-semibold">Thông tin khách hàng</div>
                <div class="card-body">
                    @if($payment->order && $payment->order->user)
                        <p><strong>Khách hàng:</strong> {{ $payment->order->user->fullname }}</p>
                        <p><strong>Email:</strong> {{ $payment->order->user->email }}</p>
                        <p><strong>Điện thoại:</strong> {{ $payment->order->user->phone }}</p>
                        <p><strong>Điểm thưởng hiện tại:</strong> {{ $payment->order->user->points ?? 0 }} điểm</p>
                    @else
                        <span class="text-muted">Không có thông tin khách hàng</span>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-light fw-semibold">Chi tiết đơn hàng</div>
                <div class="card-body">
                    @if($payment->order)
                        <p><strong>Tổng giá trị:</strong> {{ number_format($payment->order->total_price,0,',','.') }} {{ $payment->order->currency ?? '₫' }}</p>
                        <p><strong>Phí ship:</strong> {{ number_format($payment->order->shipping_fee,0,',','.') }}</p>
                        @if($payment->order->voucher)
                            <p><strong>Voucher giảm:</strong> -{{ $payment->order->voucher->discount }}%</p>
                        @endif
                        <p><strong>Tổng sau giảm:</strong> 
                            {{ number_format($payment->order->total_price + $payment->order->shipping_fee - ($payment->order->voucher?->discount ? ($payment->order->total_price * $payment->order->voucher->discount / 100) : 0), 0, ',', '.') }}
                        </p>
                    @else
                        <span class="text-muted">Không có chi tiết đơn hàng</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
