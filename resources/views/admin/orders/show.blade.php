@extends('admin.layouts.app')
@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">
            <i class="bi bi-receipt"></i> Chi tiết đơn hàng #{{ $order->code }}
        </h3>
        <div class="mb-4">
            @if($order->staff)
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-person-check me-2"></i>
                    <div>
                        <strong>Đã xác nhận bởi:</strong> {{ $order->staff->fullname ?? $order->staff->username }}
                        <span class="text-muted">(ID: {{ $order->staff_id }})</span>
                    </div>
                </div>
            @elseif($order->confirm_by)
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-person-check me-2"></i>
                    <div><strong>Đã xác nhận bởi:</strong> {{ $order->confirm_by }}</div>
                </div>
            @else
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-hourglass-split me-2"></i>
                    <div>Đơn hàng chưa được nhân viên nào xác nhận.</div>
                </div>
            @endif
        </div>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-danger">
                <i class="bi bi-filetype-pdf"></i> Tải PDF
            </a>
        </div>
    </div>

    {{-- ===== THÔNG TIN NGƯỜI ĐẶT / NHẬN ===== --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold">Thông tin người đặt</div>
                <div class="card-body">
                    <p><strong>Họ tên:</strong> {{ $order->placed_name ?? $order->fullname }}</p>
                    <p><strong>SĐT:</strong> {{ $order->placed_phone ?? $order->phone }}</p>
                    <p><strong>Email:</strong> {{ $order->placed_email ?? ($order->user->email ?? '-') }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->placed_address ?? $order->address }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white fw-bold">Thông tin người nhận</div>
                <div class="card-body">
                    <p><strong>Họ tên:</strong> {{ $order->receiver_name ?? $order->fullname }}</p>
                    <p><strong>SĐT:</strong> {{ $order->receiver_phone ?? $order->phone }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->receiver_address ?? $order->address }}</p>
                    <p><strong>Ghi chú:</strong> {{ $order->note ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== DANH SÁCH SẢN PHẨM & TỔNG TIỀN ===== --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-light fw-bold">Sản phẩm trong đơn</div>

        @php
            // Tính toán tổng tiền sản phẩm
            $totalProducts = 0;
            foreach ($order->orderDetails ?? [] as $item) {
                $totalProducts += ($item->price * $item->quantity);
            }

            // Giảm giá từ voucher (nếu có)
            $discount = 0;
            if ($order->voucher) {
                if ($order->voucher->type === 'percent') {
                    $discount = $totalProducts * ($order->voucher->discount / 100);
                } else {
                    $discount = $order->voucher->discount;
                }
            }

            // Phí vận chuyển
            $shipping = $order->shipping_fee ?? 0;

            // Tổng thanh toán cuối cùng
            $grandTotal = $totalProducts - $discount + $shipping;
        @endphp

        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Sản phẩm</th>
                        <th>Phân loại</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th class="text-end">Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->orderDetails ?? [] as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product->name ?? 'Sản phẩm đã xóa' }}</td>
                            <td>{{ $item->variant->name ?? 'N/A' }}</td>
                            <td>{{ number_format($item->price) }} ₫</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="text-end text-danger fw-semibold">
                                {{ number_format($item->price * $item->quantity) }} ₫
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                Không có sản phẩm nào trong đơn này.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tổng kết --}}
        <div class="card-footer bg-light">
            <div class="row text-end fw-semibold">
                <div class="col-9 text-end">Tổng giá sản phẩm:</div>
                <div class="col-3 text-danger">{{ number_format($totalProducts) }} ₫</div>

                <div class="col-9 text-end">Giảm từ voucher:</div>
                <div class="col-3 text-success">-{{ number_format($discount) }} ₫</div>

                <div class="col-9 text-end">Phí vận chuyển:</div>
                <div class="col-3 text-primary">{{ number_format($shipping) }} ₫</div>

                <div class="col-12"><hr></div>

                <div class="col-9 text-end fs-5">Tổng thanh toán:</div>
                <div class="col-3 fs-5 text-danger">
                    <strong>{{ number_format($grandTotal) }} ₫</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== THÔNG TIN THANH TOÁN ===== --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-secondary text-white fw-bold">Thông tin thanh toán</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <p><strong>Trạng thái:</strong></p>
                    <span class="badge bg-{{ $order->status_color }} fs-6">
                        {{ $order->status_label }}
                    </span>
                </div>
                <div class="col-md-4">
                    <p><strong>Voucher:</strong></p>
                    @if($order->voucher)
                        <span class="text-success">{{ $order->voucher->name }} ({{ $order->voucher->code }}) - Giảm {{ $order->voucher->discount_display }}</span>
                    @else
                        <span class="text-muted">Không sử dụng voucher</span>
                    @endif
                </div>
                <div class="col-md-4">
                    <p><strong>Trạng thái thanh toán:</strong></p>
                    @if($order->payment)
                        @php
                            $paymentStatusColors = [
                                'pending' => 'warning',
                                'paid' => 'success',
                                'refunded' => 'secondary',
                                'failed' => 'danger',
                            ];
                            $color = $paymentStatusColors[$order->payment->status_label ] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }} fs-6">
                            {{ ucfirst($order->payment->status_label) }}
                        </span>
                    @else
                        <span class="text-muted">Chưa có thanh toán</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== GHI CHÚ NỘI BỘ ===== --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-warning fw-bold">
            Ghi chú nội bộ
        </div>
        <div class="card-body">
            <form action="{{ route('admin.orders.addNote', $order->id) }}" method="POST">
                @csrf
                <textarea name="internal_note" class="form-control mb-3" rows="3" placeholder="Thêm ghi chú nội bộ...">{{ old('internal_note', $order->internal_note ?? '') }}</textarea>
                <button class="btn btn-sm btn-primary">
                    <i class="bi bi-save"></i> Lưu ghi chú
                </button>
            </form>
        </div>
    </div>

    {{-- ===== CẬP NHẬT TRẠNG THÁI ===== --}}
    @php
        // Map tiếng Việt cho trạng thái
        $statusLabels = [
            'pending'   => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'shipping'  => 'Đang giao hàng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'failed'    => 'Thất bại',
            'returned'  => 'Đã trả hàng',
        ];

        // Các trạng thái có thể chuyển
        $allowedTransitions = [
            'pending'   => ['confirmed', 'cancelled', 'failed'],
            'confirmed' => ['shipping', 'cancelled'],
            'shipping'  => ['completed', 'failed', 'returned'],
            'completed' => [],
            'cancelled' => [],
            'failed'    => [],
            'returned'  => [],
        ];

        $currentStatus = $order->status;
        $possibleStatuses = $allowedTransitions[$currentStatus] ?? [];
    @endphp

    @if(!empty($possibleStatuses))
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-info text-white fw-bold">Cập nhật trạng thái đơn hàng</div>
            <div class="card-body">
                <form id="status-update-form">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label for="status-select" class="form-label fw-semibold">Chọn trạng thái mới:</label>
                            <select id="status-select" class="form-select" name="status">
                                <option value="{{ $currentStatus }}" disabled selected>
                                    {{ $statusLabels[$currentStatus] ?? ucfirst($currentStatus) }} (Trạng thái hiện tại)
                                </option>
                                @foreach($possibleStatuses as $status)
                                    <option value="{{ $status }}">
                                        {{ $statusLabels[$status] ?? ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-arrow-repeat"></i> Cập nhật trạng thái
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-info mt-4">
            <i class="bi bi-info-circle"></i> Đơn hàng đã ở trạng thái cuối cùng và không thể cập nhật thêm.
        </div>
    @endif

</div>

{{-- ===== AJAX CẬP NHẬT TRẠNG THÁI ===== --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('status-update-form');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const select = document.getElementById('status-select');
            const newStatus = select.value;
            if (!newStatus || select.selectedIndex === 0) {
                alert('Vui lòng chọn trạng thái mới.');
                return;
            }
            if (!confirm(`Bạn có chắc muốn thay đổi trạng thái đơn hàng thành "${newStatus}"?`)) return;

            const id = {{ $order->id }};  // Lấy ID từ Blade
            const res = await fetch(`/admin/orders/${id}/ajax-update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: newStatus })
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message);
                location.reload();  // Reload để cập nhật UI
            } else {
                alert('Lỗi: ' + (data.message || 'Không xác định'));
            }
        });
    }
});
</script>
@endsection