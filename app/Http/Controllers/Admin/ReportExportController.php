<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Exports\MultiFilteredExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController extends Controller
{
    public function export(Request $request, string $type)
    {
        return Excel::download(new MultiFilteredExport($request, $type), "{$type}-report.xlsx");
    }
}
