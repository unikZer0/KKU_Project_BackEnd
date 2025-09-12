<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;

class LogController extends Controller
{
    public function index(Request $request) // <- Inject Request here
    {
        $logs = Log::with('admin')
            ->when($request->admin, fn($q) => $q->whereHas('admin', fn($a) => $a->where('name', 'like', "%{$request->admin}%")))
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->when($request->target_type, fn($q) => $q->where('target_type', $request->target_type))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.report.log', compact('logs'));
    }
}
