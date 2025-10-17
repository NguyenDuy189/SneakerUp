@extends('admin.layouts.app')

@section('title', 'Quản lý đơn hàng')

@section('content')
<style>
    .order-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }
    .order-header h1 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #0d6efd;
    }
    .filter-form .form-control, .filter-form .form-select {
        border-radius: 8px;
    }
    .table thead {
        background-color: #0d6efd;
        color: #fff;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transition: 0.2s;
    }
    .badge {
        text-transform: capitalize;
        font-size: 0.9rem;
        padding: 0.5em 0.75em;
        border-radius: 8px;
    }
    .btn {
        border-radius: 6px;
    }
    .action-btns .btn {
        padding: 4px 10px;
    }
    .pagination {
        justify-content: center;
    }
    .search-box {
        max-width: 250px;
    }

</style>

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="order-header">
        <h1><i class="bi bi-box-seam"></i> Quản lý đơn hàng</h1>
        <div>
            <a href="{{ route('admin.orders.export.csv') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-filetype-csv"></i> CSV
            </a>
            <a href="{{ route('admin.orders.export.excel') }}" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
        </div>
    </div>

    {{-- Bộ lọc --}}
    <form method="GET" class="row g-2 align-items-center mb-4 filter-form">
        <div class="col-auto search-box">
            <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Tìm mã đơn / tên / SĐT">
        </div>
        <div class="col-auto">
            <select name="status" class="form-select">
                <option value="">-- Trạng thái --</option>
                @foreach(['pending','confirmed','shipping','completed','cancelled','failed'] as $s)
                    <option value="{{ $s }}" @selected(request('status')==$s)>
                        {{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">
                <i class="bi bi-funnel"></i> Lọc
            </button>
        </div>
    </form>

    {{-- Bảng danh sách --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr id="order-{{ $order->id }}">
                                <td class="fw-semibold text-primary">{{ $order->code }}</td>
                                <td>
                                    <div>{{ $order->fullname }}</div>
                                    <small class="text-muted">{{ $order->phone }}</small>
                                </td>
                                <td class="fw-bold text-danger">{{ number_format($order->total_price) }} ₫</td>
                                <td>{{ ucfirst($order->payment_method) }}</td>
                                <td>
                                    @php
                                        $statusColor = [
                                            'pending' => 'warning',
                                            'confirmed' => 'info',
                                            'shipping' => 'primary',
                                            'completed' => 'success',
                                            'cancelled' => 'secondary',
                                            'failed' => 'danger'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColor[$order->status] ?? 'secondary' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="text-center action-btns">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>

                                    @switch($order->status)
                                        @case('pending')
                                            <button class="btn btn-sm btn-success js-ajax-confirm" 
                                                    data-id="{{ $order->id }}" data-status="confirmed">
                                                <i class="bi bi-check2-circle"></i> Confirm
                                            </button>
                                            @break

                                        @case('confirmed')
                                            <button class="btn btn-sm btn-primary js-ajax-confirm"
                                                    data-id="{{ $order->id }}" data-status="shipping">
                                                <i class="bi bi-truck"></i> Shipping
                                            </button>
                                            @break

                                        @case('shipping')
                                            <button class="btn btn-sm btn-success js-ajax-confirm"
                                                    data-id="{{ $order->id }}" data-status="completed">
                                                <i class="bi bi-check2"></i> Complete
                                            </button>
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox"></i> Không có đơn hàng nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-light">
            {{ $orders->links() }}
        </div>
    </div>
</div>

{{-- Script cập nhật trạng thái --}}
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.js-ajax-confirm').forEach(btn => {
        btn.addEventListener('click', async function(){
            const id = this.dataset.id;
            const status = this.dataset.status;
            if(!confirm('Xác nhận đổi trạng thái đơn hàng #' + id + ' ?')) return;
            const url = `/admin/orders/${id}/ajax-update-status`;
            const token = '{{ csrf_token() }}';
            const res = await fetch(url, {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token},
                body: JSON.stringify({status})
            });
            const data = await res.json();
            if(data.success){
                location.reload();
            } else {
                alert('Có lỗi: '+(data.message||'Không xác định'));
            }
        });
    });
});
</script>
@endsection
