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
        $month = $request->input('month'); // optional

        $cacheKey = "admin_dashboard_{$year}_{$month}";

        $data = Cache::remember($cacheKey, 300, function () use ($year, $month) {
            $query = BorrowRequest::query();
            $query->whereYear('created_at', $year);

            if ($month) {
                $query->whereMonth('created_at', $month);
            }

            // KPI Cards
            $borrowStatus = [
                'TotalRequests' => (clone $query)->count(),
                'checkinReq' => (clone $query)->where('status', 'check_in')->count(),
                'Approved' => (clone $query)->where('status', 'approved')->count(),
                'Rejected' => (clone $query)->where('status', 'rejected')->count(),
                'Pending' => (clone $query)->where('status', 'pending')->count(),
            ];

            // Chart data
            if ($month) {
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                $dailyData = BorrowRequest::selectRaw('DAY(created_at) as day, COUNT(*) as total, SUM(status="approved") as approved, SUM(status="rejected") as rejected')
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->groupBy('day')
                    ->orderBy('day')
                    ->get()
                    ->keyBy('day');

                $chartData = [
                    'labels' => array_map(fn($d) => "Day $d", range(1, $daysInMonth)),
                    'TotalRequests' => [],
                    'Approved' => [],
                    'Rejected' => [],
                ];

                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $chartData['TotalRequests'][] = $dailyData[$d]->total ?? 0;
                    $chartData['Approved'][] = $dailyData[$d]->approved ?? 0;
                    $chartData['Rejected'][] = $dailyData[$d]->rejected ?? 0;
                }
            } else {
                $months = range(1, 12);

                $monthlyData = BorrowRequest::selectRaw('MONTH(created_at) as month, COUNT(*) as total, SUM(status="approved") as approved, SUM(status="rejected") as rejected')
                    ->whereYear('created_at', $year)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get()
                    ->keyBy('month');

                $chartData = [
                    'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                    'TotalRequests' => [],
                    'Approved' => [],
                    'Rejected' => [],
                ];

                foreach ($months as $m) {
                    $chartData['TotalRequests'][] = $monthlyData[$m]->total ?? 0;
                    $chartData['Approved'][] = $monthlyData[$m]->approved ?? 0;
                    $chartData['Rejected'][] = $monthlyData[$m]->rejected ?? 0;
                }
            }

            $availableYears = BorrowRequest::selectRaw('YEAR(created_at) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');

            $recentRequests = (clone $query)->with('user', 'equipment')->latest()->take(5)->get()->map(function($r) {
                return [
                    'id' => $r->req_id,
                    'user_name' => $r->user->name ?? 'N/A',
                    'equipment_name' => $r->equipment->name ?? 'N/A',
                    'start' => optional($r->start_at)->format('Y-m-d'),
                    'end' => optional($r->end_at)->format('Y-m-d'),
                    'status' => ucfirst($r->status),
                ];
            });

            return [
                'borrowStatus' => $borrowStatus,
                'chartData' => $chartData,
                'availableYears' => $availableYears,
                'selectedYear' => $year,
                'selectedMonth' => $month,
                'recentRequests' => $recentRequests,
                'categoryCounts' => Category::withCount('equipments')->get(),
            ];
        });

        return view('admin.index', $data);
    }
}
