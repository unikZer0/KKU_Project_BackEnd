<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MultiFilteredExport;
use App\Models\Equipment;
use App\Models\Category;
use App\Models\User;
use App\Models\BorrowRequest;
use App\Models\Log;

class ReportController extends Controller
{
    //! Vue
    public function vueReport(string $type)
    {
        return view('admin.report.vue-wrapper', ['type' => $type]);
    }
    //! Report
    public function userReport()
    {
        $users = User::all()->map(fn($user) => [
            'uid' => $user->uid,
            'name' => $user->name,
            'email' => $user->email,
            'phonenumber' => $user->phonenumber ?? '-',
            'created_at' => optional($user->created_at)->format('d/m/Y'),
        ]);
        return response()->json($users);
    }
    //! Equipment
    public function equipmentReport()
    {
        $equipments = Equipment::with('category')->get()->map(fn($eq) => [
            'id' => $eq->id,
            'code' => $eq->code,
            'name' => $eq->name,
            'category_name' => $eq->category->name ?? 'N/A',
            'created_at' => optional($eq->created_at)->format('d/m/Y'),
        ]);
        return response()->json($equipments);
    }
    //! Category
    public function categoryReport()
    {
        $categories = Category::all()->map(fn($cat) => [
            'id' => $cat->id,
            'name' => $cat->name,
        ]);
        return response()->json($categories);
    }
    //! Request
    public function requestReport()
    {
        $requests = BorrowRequest::with(['user', 'equipment'])
            ->whereIn('status', ['approved', 'rejected', 'cancelled'])
            ->latest()
            ->get()
            ->map(fn($req) => [
                'req_id' => $req->req_id,
                'user_name' => $req->user->name ?? 'N/A',
                'equipment_name' => $req->equipment->name ?? 'N/A',
                'date' => $req->created_at->format('Y-m-d'),
                'status' => ucfirst($req->status),
                'reason' => $req->reject_reason ?? $req->cancel_reason ?? '-',
            ]);
        return response()->json($requests);
    }
    //! Log
    public function logReport(Request $request)
    {
        $logs = Log::with('admin')
            ->when($request->admin, fn($q) => $q->whereHas('admin', fn($a) => $a->where('name', 'like', "%{$request->admin}%")))
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->when($request->target_type, fn($q) => $q->where('target_type', $request->target_type))
            ->when($request->module, fn($q) => $q->where('module', $request->module))
            ->when($request->severity, fn($q) => $q->where('severity', $request->severity))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($logs);
    }

    //! Log Statistics
    public function logStats()
    {
        $stats = [
            'total_logs' => Log::count(),
            'today_logs' => Log::whereDate('created_at', today())->count(),
            'this_week_logs' => Log::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month_logs' => Log::whereMonth('created_at', now()->month)->count(),
            'top_actions' => Log::selectRaw('action, COUNT(*) as count')
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'top_modules' => Log::selectRaw('module, COUNT(*) as count')
                ->groupBy('module')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'severity_distribution' => Log::selectRaw('severity, COUNT(*) as count')
                ->groupBy('severity')
                ->orderBy('count', 'desc')
                ->get(),
            'recent_activities' => Log::with('admin')
                ->latest()
                ->limit(10)
                ->get()
        ];

        return response()->json($stats);
    }

    //? Export
    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:user,equipment,category,request,log',
        ]);

        $type = $request->input('type');
        $filename = "{$type}-report.xlsx";

        return Excel::download(new MultiFilteredExport($request, $type), $filename);
    }
}
