<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\BorrowRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $cacheKey = "admin_dashboard_{$year}";

        $data = Cache::remember($cacheKey, 300, function () use ($year) {
            $recentRequests = BorrowRequest::with('user', 'equipment')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($r) {
                    return [
                        'id' => $r->req_id,
                        'user_name' => $r->user->name ?? 'N/A',
                        'equipment_name' => $r->equipment->name ?? 'N/A',
                        'start' => optional($r->start_at)->format('Y-m-d'),
                        'end' => optional($r->end_at)->format('Y-m-d'),
                        'status' => ucfirst($r->status),
                    ];
                });

            $borrowStatus = [
                'TotalRequests' => BorrowRequest::count(),
                'Approved' => BorrowRequest::where('status', 'approved')->count(),
                'Rejected' => BorrowRequest::where('status', 'rejected')->count(),
                'Pending' => BorrowRequest::where('status', 'pending')->count(),
            ];

            $monthlyData = BorrowRequest::selectRaw('
                    MONTH(created_at) as month,
                    COUNT(*) as total,
                    SUM(status = "approved") as approved,
                    SUM(status = "rejected") as rejected
                ')
                ->whereYear('created_at', $year)
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            $months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
            $borrowStatusMonthly = [
                'months' => $months,
                'TotalRequests' => [],
                'Approved' => [],
                'Rejected' => [],
            ];

            for ($month = 1; $month <= 12; $month++) {
                $borrowStatusMonthly['TotalRequests'][] = $monthlyData[$month]->total ?? 0;
                $borrowStatusMonthly['Approved'][] = $monthlyData[$month]->approved ?? 0;
                $borrowStatusMonthly['Rejected'][] = $monthlyData[$month]->rejected ?? 0;
            }

            $availableYears = BorrowRequest::selectRaw('YEAR(created_at) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');

            return [
                'totalRequests' => $borrowStatus['TotalRequests'],
                'pendingRequests' => $borrowStatus['Pending'],
                'penaltyNotices' => BorrowRequest::where('status', 'overdue')->count(),
                'borrowStatus' => $borrowStatus,
                'borrowStatusMonthly' => $borrowStatusMonthly,
                'recentRequests' => $recentRequests,
                'categoryCounts' => Category::withCount('equipments')->get(),
                'selectedYear' => $year,
                'availableYears' => $availableYears,
            ];
        });

        return view('admin.index', $data);
    }
}
