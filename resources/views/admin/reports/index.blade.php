@extends('admin.layouts.app')

@section('title', 'Smart Analytics Premium — SneakerUp')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .card-hero { border-radius: 12px; box-shadow: 0 8px 28px rgba(15,23,42,0.06); padding: 18px; background:#fff; }
    .stat-value { font-size: 28px; font-weight: 700; }
    .filters .form-control, .filters .select2 { min-width: 180px; }
    .chart-card { border-radius: 12px; padding: 18px; background: #fff; box-shadow: 0 6px 20px rgba(2,6,23,0.04); }
    .btn-export { min-width: 110px; }
    .select2-container--default .select2-selection--single { height: 36px; padding: 4px 8px; }
    .table td, .table th { vertical-align: middle; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    {{-- Header + Filters --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="fw-bold"><i class="bi bi-graph-up"></i> Smart Analytics Premium</h3>
        <div class="d-flex gap-2 align-items-center filters flex-wrap">
            <select id="presetRange" class="form-select form-select-sm">
                <option value="7">7 ngày</option>
                <option value="30" selected>30 ngày</option>
                <option value="90">90 ngày</option>
            </select>

            <input type="date" id="startDate" class="form-control form-control-sm">
            <input type="date" id="endDate" class="form-control form-control-sm">

            <select id="customerFilter" class="form-select form-select-sm"></select>
            <select id="categoryFilter" class="form-select form-select-sm"></select>

            <button id="applyFilter" class="btn btn-primary btn-sm">Áp dụng</button>

            <div class="btn-group ms-2">
                <a id="exportCsv" href="#" class="btn btn-outline-success btn-sm btn-export"><i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV</a>
                <a id="exportExcel" href="#" class="btn btn-outline-success btn-sm btn-export"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
                <a id="exportPdf" href="#" class="btn btn-outline-danger btn-sm btn-export"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
            </div>
        </div>
    </div>

    {{-- Overview Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card-hero chart-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted">Doanh thu</div>
                        <div class="stat-value text-success" id="cardRevenue">₫0</div>
                        <div class="small text-muted">Tổng doanh thu trong khoảng</div>
                    </div>
                    <div class="text-end">
                        <i class="bi bi-currency-dollar" style="font-size:34px;color:#10b981"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-hero chart-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted">Đơn hàng</div>
                        <div class="stat-value" id="cardOrders">0</div>
                        <div class="small text-muted">Tổng đơn trong khoảng</div>
                    </div>
                    <div class="text-end">
                        <i class="bi bi-bag-check" style="font-size:34px;color:#0d6efd"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-hero chart-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted">Khách hàng hoạt động</div>
                        <div class="stat-value" id="cardCustomers">0</div>
                        <div class="small text-muted">6 tháng gần nhất</div>
                    </div>
                    <div class="text-end">
                        <i class="bi bi-people" style="font-size:34px;color:#f59e0b"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts & Tables --}}
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="chart-card mb-3">
                <h5 class="mb-2">Doanh thu & Đơn hàng theo ngày</h5>
                <div id="chartSalesTrend" style="height:350px;"></div>
            </div>

            <div class="chart-card">
                <h5 class="mb-2">Top sản phẩm (Số lượng & Doanh thu)</h5>
                <div id="chartProducts" style="height:350px;"></div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="chart-card mb-3">
                <h5>Top khách hàng</h5>
                <table class="table table-borderless table-hover">
                    <thead><tr><th>#</th><th>Khách hàng</th><th>Đơn</th><th>Chi tiêu</th></tr></thead>
                    <tbody id="topCustomersBody"></tbody>
                </table>
            </div>
            <div class="chart-card">
                <h5>Quick Insights</h5>
                <ul id="quickInsights" class="list-unstyled mb-0">
                    <li class="py-2 border-bottom">Tỉ lệ hoàn / hủy: <strong id="insightReturns">—</strong></li>
                    <li class="py-2 border-bottom">Sản phẩm sắp hết: <strong id="insightLowStock">—</strong></li>
                    <li class="py-2">Tỉ lệ trung bình đơn hàng: <strong id="insightAov">—</strong></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const api = {
        salesTrend: '{{ route("admin.api.dashboard.salesTrend") }}',
        productAnalysis: '{{ route("admin.api.dashboard.productAnalysis") }}',
        topCustomers: '{{ route("admin.api.dashboard.topCustomers") }}',
        stats: '{{ route("admin.api.dashboard.stats") }}',
        customers: '{{ route("admin.api.customers") }}',
        categories: '{{ route("admin.api.categories") }}'
    };

    const startDateEl = document.getElementById('startDate');
    const endDateEl = document.getElementById('endDate');
    const presetEl = document.getElementById('presetRange');
    const applyBtn = document.getElementById('applyFilter');
    
    // Dòng này bây giờ sẽ chạy đúng vì jQuery (biểu tượng $) đã được tải
    const customerEl = $('#customerFilter');
    const categoryEl = $('#categoryFilter');

    let salesChart, productsChart;

    // Select2 AJAX (Dòng này cần jQuery để chạy)
    customerEl.select2({placeholder:'Chọn khách hàng',ajax:{url:api.customers,dataType:'json',delay:250,data:params=>({q:params.term})},allowClear:true,width:'100%'});
    categoryEl.select2({placeholder:'Chọn danh mục',ajax:{url:api.categories,dataType:'json',delay:250,data:params=>({q:params.term})},allowClear:true,width:'100%'});

    function setDefaultRange(days=30){
        const end=new Date(), start=new Date();
        start.setDate(end.getDate()-(days-1));
        startDateEl.value=start.toISOString().slice(0,10);
        endDateEl.value=end.toISOString().slice(0,10);
    }
    setDefaultRange(30);

    const exportCsv = document.getElementById('exportCsv');
    const exportExcel = document.getElementById('exportExcel');
    const exportPdf = document.getElementById('exportPdf');
    function setExportLinks(){
        const s=startDateEl.value,e=endDateEl.value;
        exportCsv.href="{{ route('admin.reports.export.csv') }}?start_date="+s+"&end_date="+e;
        exportExcel.href="{{ route('admin.reports.export.excel') }}?start_date="+s+"&end_date="+e;
        exportPdf.href="{{ route('admin.reports.export.pdf') }}?start_date="+s+"&end_date="+e;
    }
    setExportLinks();

    async function loadStats(){
        const s=startDateEl.value,e=endDateEl.value;
        const res=await fetch(api.stats+`?start_date=${s}&end_date=${e}`);
        const json=await res.json();
        const d=json.data;
        document.getElementById('cardRevenue').innerText=new Intl.NumberFormat('vi-VN').format(d.revenue)+' ₫';
        document.getElementById('cardOrders').innerText=d.orders;
        document.getElementById('cardCustomers').innerText=d.active_customers;
    }

    async function loadSalesTrend(){
        const s=startDateEl.value,e=endDateEl.value;
        const customer=customerEl.val(), category=categoryEl.val();
        let url=api.salesTrend+`?start_date=${s}&end_date=${e}`;
        if(customer) url+=`&customer_id=${customer}`;
        if(category) url+=`&category_id=${category}`;
        const res=await fetch(url); const json=await res.json();
        const labels=json.data.map(i=>i.date);
        const revenue=json.data.map(i=>i.revenue);
        const orders=json.data.map(i=>i.orders);
        if(salesChart)salesChart.destroy();
        salesChart=new ApexCharts(document.querySelector('#chartSalesTrend'),{
            chart:{type:'area',height:350,toolbar:{show:true}},
            series:[{name:'Doanh thu',data:revenue},{name:'Đơn hàng',data:orders}],
            xaxis:{categories:labels},
            stroke:{curve:'smooth'},
            colors:['#0d6efd','#10b981'],
            yaxis:[{title:{text:'Doanh thu ₫'}},{opposite:true,title:{text:'Đơn hàng'}}],
            tooltip:{shared:true,y:{formatter:val=>new Intl.NumberFormat('vi-VN').format(val)}}
        });
        salesChart.render();
    }

    async function loadProductAnalysis(){
        const s=startDateEl.value,e=endDateEl.value;
        const customer=customerEl.val(), category=categoryEl.val();
        let url=api.productAnalysis+`?start_date=${s}&end_date=${e}`;
        if(customer) url+=`&customer_id=${customer}`;
        if(category) url+=`&category_id=${category}`;
        const res=await fetch(url); const json=await res.json();
        const names=json.data.map(i=>i.product_name);
        const sold=json.data.map(i=>parseInt(i.total_sold,10));
        const revenue=json.data.map(i=>parseFloat(i.total_revenue));
        if(productsChart) productsChart.destroy();
        productsChart=new ApexCharts(document.querySelector('#chartProducts'),{
            chart:{type:'bar',height:350},
            series:[{name:'Số lượng',data:sold},{name:'Doanh thu',data:revenue}],
            xaxis:{categories:names},colors:['#10b981','#f59e0b'],
            yaxis:[{title:{text:'Số lượng'}},{opposite:true,title:{text:'Doanh thu ₫'}}],
            tooltip:{y:{formatter:val=>new Intl.NumberFormat('vi-VN').format(val)}}
        });
        productsChart.render();
    }

    async function loadTopCustomers(){
        const s=startDateEl.value,e=endDateEl.value;
        let url=api.topCustomers+`?limit=10&start_date=${s}&end_date=${e}`;
        if(customerEl.val()) url+=`&customer_id=${customerEl.val()}`;
        const res=await fetch(url); const json=await res.json();
        const tbody=document.getElementById('topCustomersBody');
        tbody.innerHTML=json.data.map((r,i)=>`
            <tr>
                <td>${i+1}</td>
                <td><div class="d-flex align-items-center">
                    <img src="https://i.pravatar.cc/40?u=${r.id}" class="rounded-circle me-2" width="36" height="36">
                    <div>${r.name}</div>
                </div></td>
                <td>${r.total_orders}</td>
                <td>₫${new Intl.NumberFormat('vi-VN').format(r.total_spent)}</td>
            </tr>
        `).join('');
    }

    async function loadQuickInsights(){
        document.getElementById('insightReturns').innerText='2.4%';
        document.getElementById('insightLowStock').innerText='12 sản phẩm';
        document.getElementById('insightAov').innerText='₫'+new Intl.NumberFormat('vi-VN').format(450000);
    }

    async function init(){
        await loadStats();
        await loadSalesTrend();
        await loadProductAnalysis();
        await loadTopCustomers();
        await loadQuickInsights();
    }
    init();

    applyBtn.addEventListener('click',()=>{
        setExportLinks();
        init();
    });
});
</script>
@endpush