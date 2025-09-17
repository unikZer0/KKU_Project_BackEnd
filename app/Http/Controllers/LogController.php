<?php

namespace App\Http\Controllers;

use App\Exports\LogsExport;
use Illuminate\Http\Request;
use App\Models\Log;

use Maatwebsite\Excel\Facades\Excel;

class LogController extends Controller
{
    public function logReport(Request $request) // <- Inject Request here
    {
        $logs = Log::with('admin')
            ->when($request->admin, fn($q) => $q->whereHas('admin', fn($a) => $a->where('name', 'like', "%{$request->admin}%")))
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->when($request->target_type, fn($q) => $q->where('target_type', $request->target_type))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.report.log', compact('logs'));
    }
    public function exportLogs()
    {
        return Excel::download(new LogsExport, 'รายงานบันทึกแอดมิน.xlsx');
    }
}
