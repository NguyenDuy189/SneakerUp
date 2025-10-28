@extends('admin.layouts.app')

@section('title', 'Dashboard — SneakerUp')

@section('content')
<div class="container-fluid py-4">

    {{-- Header area: presets + filters + actions --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4 gap-2">
        <div class="d-flex gap-2 align-items-center">
            <h4 class="mb-0 fw-bold">Dashboard</h4>
            <small class="text-muted">| Tổng quan & báo cáo</small>
        </div>

        <div class="d-flex gap-4 align-items-center">
            {{-- quick presets --}}
            <div class="btn-group me-2" role="group">
                <button class="btn btn-outline-secondary btn-sm preset" data-range="7">7 ngày</button>
                <button class="btn btn-outline-secondary btn-sm preset" data-range="30">30 ngày</button>
                <button class="btn btn-outline-secondary btn-sm preset" data-range="90">90 ngày</button>
            </div>

            {{-- date filter --}}
            <form id="filterForm" class="d-flex gap-4 align-items-end">
                <div>
                    <label class="form-label small mb-1">Từ</label>
                    <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                </div>
                <div>
                    <label class="form-label small mb-1">Đến</label>
                    <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                </div>
                <div class="align-self-end">
                    <button type="button" id="applyFilter" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-filter me-1"></i> Áp dụng
                    </button>
                </div>
            </form>

            {{-- actions: export / refresh --}}
            <div class="ms-2">
                <div class="btn-group">
                    <button id="refreshBtn" class="btn btn-outline-primary btn-sm" title="Refresh"><i class="fa-solid fa-arrows-rotate"></i></button>
                    <button class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-download me-1"></i> Export</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" id="exportCsv" href="{{ route('admin.dashboard.export.csv', ['start_date'=>$startDate,'end_date'=>$endDate]) }}">CSV</a></li>
                        <li><a class="dropdown-item" id="exportExcel" href="{{ route('admin.dashboard.export.excel', ['start_date'=>$startDate,'end_date'=>$endDate]) }}">Excel</a></li>
                        <li><a class="dropdown-item" id="exportPdf" href="{{ route('admin.dashboard.export.pdf', ['start_date'=>$startDate,'end_date'=>$endDate]) }}">PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Cards --}}
    <div class="row g-4 mb-4">
        @php
            $cards = [
                ['title'=>'Tổng Doanh Thu','value'=>number_format($summary['totalRevenue'],0,',','.').' đ','icon'=>'fa-sack-dollar','color'=>'bg-gradient-success'],
                ['title'=>'Tổng Đơn Hàng','value'=>$summary['totalOrders'],'icon'=>'fa-cart-shopping','color'=>'bg-gradient-primary'],
                ['title'=>'Tổng Khách Hàng','value'=>$summary['totalUsers'],'icon'=>'fa-users','color'=>'bg-gradient-info'],
                ['title'=>'Tổng Sản Phẩm','value'=>$summary['totalProducts'],'icon'=>'fa-box','color'=>'bg-gradient-warning'],
            ];

            // map trạng thái sang tiếng Việt & màu badge
            $statusMap = [
                'pending'   => 'Chờ xử lý',
                'confirmed' => 'Đã xác nhận',
                'shipping'  => 'Đang giao',
                'completed' => 'Hoàn tất',
                'cancelled' => 'Đã hủy',
                'failed'    => 'Thất bại',
                'returned'  => 'Đã hoàn trả',
            ];
            $badgeMap = [
                'pending' => 'warning',
                'confirmed' => 'info',
                'shipping' => 'primary',
                'completed' => 'success',
                'cancelled' => 'danger',
                'failed' => 'danger',
                'returned' => 'secondary',
            ];
        @endphp

        @foreach($cards as $c)
            <div class="col-xl-3 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex gap-3 align-items-center">
                        <div class="flex-shrink-0 rounded-circle p-3 text-white {{ $c['color'] }}" style="width:72px;height:72px;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 24px rgba(15,23,42,.08);">
                            <i class="fa-solid {{ $c['icon'] }} fa-2x"></i>
                        </div>
                        <div>
                            <div class="small text-muted">{{ $c['title'] }}</div>
                            <div class="h5 fw-bold mb-0">{{ $c['value'] }}</div>
                            <small class="text-muted">Cập nhật: {{ now()->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Charts area --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-chart-area me-2 text-primary"></i> Doanh thu & Đơn hàng (Tháng)</h5>
                        <div class="d-flex gap-4 align-items-center">
                            <select id="yearSelect" class="form-select form-select-sm">
                                @for($y = now()->year; $y >= now()->year - 4; $y--)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <canvas id="combinedChart" height="160"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-chart-pie me-2 text-warning"></i> Trạng thái đơn hàng</h5>
                    <canvas id="statusPie" height="220"></canvas>

                    <hr>

                    <h6 class="fw-semibold mb-2">Top sản phẩm</h6>
                    <div id="topProducts" class="list-group list-group-flush">
                        @foreach($topProducts as $p)
                            <div class="list-group-item d-flex justify-content-between align-items-center border-0">
                                <div><strong>{{ $p->name }}</strong><div class="small text-muted">Số lượng: {{ $p->sold }}</div></div>
                                <div class="badge bg-primary rounded-pill">{{ $p->sold }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent orders table --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-receipt me-2 text-info"></i> Đơn hàng mới nhất</h5>
                <small class="text-muted">Hiển thị {{ $recentOrders->count() }} đơn gần nhất</small>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        @foreach($recentOrders as $o)
                            @php
                                $st = $o->status;
                                $label = $statusMap[$st] ?? ucfirst($st);
                                $badge = $badgeMap[$st] ?? 'secondary';
                            @endphp
                            <tr>
                                <td>{{ $o->id }}</td>
                                <td><strong>{{ $o->code }}</strong></td>
                                <td>{{ $o->fullname }}</td>
                                <td>{{ number_format($o->total_price, 0, ',', '.') }} đ</td>
                                <td><span class="badge bg-{{ $badge }}">{{ $label }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($o->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Giữ nguyên toàn bộ nội dung cũ của bạn --}}
    {{-- Sau phần Recent orders, thêm: --}}

    <div class="row g-4 mt-4">
        {{-- Khách hàng mới --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-user-plus text-success me-2"></i> Khách hàng mới</h5>
                    <ul class="list-group list-group-flush">
                        @foreach($newCustomers as $user)
                            <li class="list-group-item border-0">
                                <strong>{{ $user->fullname }}</strong>
                                <div class="small text-muted">{{ $user->email }} — {{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}</div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- Khách hàng thân thiết --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-crown text-warning me-2"></i> Khách hàng thân thiết</h5>
                    <ul class="list-group list-group-flush">
                        @foreach($loyalCustomers as $c)
                            <li class="list-group-item border-0 d-flex justify-content-between">
                                <span>{{ $c->fullname }}</span>
                                <span class="badge bg-primary">{{ $c->total_orders }} đơn</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        
        {{-- Sản phẩm sắp hết hàng --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-box-open text-danger me-2"></i> Sản phẩm sắp hết hàng</h5>
                    <table class="table table-sm align-middle">
                        <thead class="bg-light">
                            <tr><th>Tên sản phẩm</th><th>Tồn kho</th></tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $p)
                                <tr>
                                    <td>{{ $p->name }}</td>
                                    <td><span class="badge bg-danger">{{ $p->stock }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
            
        {{-- Thông báo --}}
        <div class="row g-4 mt-4">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="fa-solid fa-bell text-danger me-2"></i> Thông báo</h5>
                        <ul class="list-group list-group-flush">
                            @forelse($notifications as $note)
                            <li class="list-group-item border-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-{{ $note['type'] }} me-2">&nbsp;</span>
                                    {{ $note['message'] }}
                                </div>
                                <small class="text-muted">{{ $note['time'] }}</small>
                            </li>
                            @empty
                            <li class="list-group-item text-muted">Không có thông báo.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
            


</div>
@endsection

@push('styles')
<style>
    /* subtle custom for gradients and lively UI */
    .bg-gradient-success { background: linear-gradient(135deg,#10b981,#16a34a); }
    .bg-gradient-primary { background: linear-gradient(135deg,#60a5fa,#2563eb); }
    .bg-gradient-info { background: linear-gradient(135deg,#60f0ff,#0ea5e9); }
    .bg-gradient-warning { background: linear-gradient(135deg,#f59e0b,#f97316); }

    /* small animation for cards */
    .card { border-radius: .85rem; overflow: hidden; }
    .card .card-body { transition: transform .18s ease, box-shadow .18s ease; }
    .card:hover .card-body { transform: translateY(-6px); box-shadow: 0 18px 40px rgba(2,6,23,.08); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const combinedCtx = document.getElementById('combinedChart').getContext('2d');
    const pieCtx = document.getElementById('statusPie').getContext('2d');

    // initial data from server side
    let monthlyRevenue = @json($monthlyRevenue);
    let monthlyOrders  = @json($monthlyOrders);
    let statusDistribution = @json($statusDistribution);

    // status keys and Vietnamese mapping (client-side)
    const statusKeys = ['pending','confirmed','shipping','completed','cancelled','failed','returned'];
    const viMap = {
        pending:   "Chờ xử lý",
        confirmed: "Đã xác nhận",
        shipping:  "Đang giao",
        completed: "Hoàn tất",
        cancelled: "Đã hủy",
        failed:    "Thất bại",
        returned:  "Đã hoàn trả"
    };

    // Combined chart (bar + line)
    const combinedChart = new Chart(combinedCtx, {
        data: {
            labels: ['Th1','Th2','Th3','Th4','Th5','Th6','Th7','Th8','Th9','Th10','Th11','Th12'],
            datasets: [
                {
                    type: 'bar',
                    label: 'Doanh thu (VND)',
                    data: monthlyRevenue,
                    backgroundColor: 'rgba(13,110,253,0.65)',
                    borderColor: '#0d6efd',
                    yAxisID: 'y',
                    hoverOffset: 8,
                },
                {
                    type: 'line',
                    label: 'Số đơn',
                    data: monthlyOrders,
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(25,135,84,0.08)',
                    tension: 0.3,
                    yAxisID: 'y1',
                    pointRadius: 4,
                }
            ]
        },
        options: {
            interaction: { mode: 'index', intersect: false },
            responsive: true,
            stacked: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.type === 'bar') {
                                return context.dataset.label + ': ' + Number(context.parsed.y).toLocaleString() + ' đ';
                            }
                            return context.dataset.label + ': ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: { position: 'left', beginAtZero: true, ticks: { callback: v => Number(v).toLocaleString() } },
                y1: { position: 'right', beginAtZero: true, grid: { drawOnChartArea: false } }
            }
        }
    });

    // Pie chart for status distribution (Vietnamese labels & tooltip show count + percent)
    const totalStatus = statusDistribution.reduce((a,b)=>a+b,0);
    const statusPie = new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: statusKeys.map(s => viMap[s] ?? s),
            datasets: [{
                data: statusDistribution,
                backgroundColor: ['#f59e0b','#60a5fa','#7c3aed','#10b981','#ef4444','#f97316','#94a3b8'],
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        // show Vietnamese labels in legend (already set in data.labels)
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const idx = context.dataIndex;
                            const key = statusKeys[idx] ?? context.label;
                            const viLabel = viMap[key] ?? context.label;
                            const value = context.raw;
                            const percent = totalStatus ? ((value / totalStatus) * 100).toFixed(1) : 0;
                            return `${viLabel}: ${value} đơn (${percent}%)`;
                        }
                    }
                }
            }
        }
    });

    // AJAX update function
    async function fetchDataAndUpdate(params = {}) {
        try {
            const resp = await axios.get("{{ route('admin.dashboard.data') }}", { params });
            const data = resp.data;

            // update charts
            combinedChart.data.datasets[0].data = data.monthlyRevenue;
            combinedChart.data.datasets[1].data = data.monthlyOrders;
            combinedChart.update();

            // update pie: update data & labels then redraw
            const newStatus = data.statusDistribution;
            statusPie.data.datasets[0].data = newStatus;
            // recompute totalStatus for tooltip percent
            const newTotal = newStatus.reduce((a,b)=>a+b,0);
            // update labels remain Vietnamese in same order
            statusPie.update();

            // top products
            const topProductsEl = document.getElementById('topProducts');
            topProductsEl.innerHTML = '';
            data.topProducts.forEach(p => {
                const item = document.createElement('div');
                item.className = 'list-group-item d-flex justify-content-between align-items-center border-0';
                item.innerHTML = `<div><strong>${p.name}</strong><div class="small text-muted">Số lượng: ${p.sold}</div></div><div class="badge bg-primary rounded-pill">${p.sold}</div>`;
                topProductsEl.appendChild(item);
            });

            // optional: you may update summary cards and recent orders here
        } catch (e) {
            console.error(e);
            alert('Không thể tải dữ liệu. Vui lòng thử lại.');
        }
    }

    // Filters & presets
    document.querySelectorAll('.preset').forEach(btn => {
        btn.addEventListener('click', () => {
            const days = parseInt(btn.dataset.range, 10);
            const end = new Date();
            const start = new Date();
            start.setDate(end.getDate() - (days - 1));
            document.getElementById('start_date').value = start.toISOString().slice(0,10);
            document.getElementById('end_date').value = end.toISOString().slice(0,10);
            document.getElementById('applyFilter').click();
        });
    });

    document.getElementById('applyFilter').addEventListener('click', () => {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;
        fetchDataAndUpdate({ start_date: start, end_date: end, year: document.getElementById('yearSelect').value });
    });

    // refresh & year change
    document.getElementById('refreshBtn').addEventListener('click', () => {
        fetchDataAndUpdate({ year: document.getElementById('yearSelect').value });
    });
    document.getElementById('yearSelect').addEventListener('change', () => {
        fetchDataAndUpdate({ year: document.getElementById('yearSelect').value });
    });

    // initial lightweight animation
    setTimeout(() => {
        combinedChart.render();
        statusPie.render();
    }, 200);
});
</script>
@endpush
