<?php

namespace App\Http\Controllers;

use App\Models\ImportDetail;
use App\Models\ExportDetail;
use Illuminate\Http\Request;

class WarehouseLogController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->type ?? 'all';

        $imports = ImportDetail::with(['variant.product'])->latest()->get();
        $exports = ExportDetail::with(['variant.product'])->latest()->get();

        if ($type == 'import') $exports = collect([]);
        if ($type == 'export') $imports = collect([]);

        return view('admin.warehouse.logs', compact('imports', 'exports', 'type'));
    }
}

