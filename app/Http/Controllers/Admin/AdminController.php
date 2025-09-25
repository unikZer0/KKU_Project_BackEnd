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
        // Get available years first
        $availableYears = BorrowRequest::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
            
        // Use the most recent year with data, or current year if none
        $defaultYear = $availableYears->isNotEmpty() ? $availableYears->first() : now()->year;
        
        $year = $request->input('year', $defaultYear);
        $month = $request->input('month'); // optional

        $cacheKey = "admin_dashboard_{$year}_{$month}";

        $data = Cache::remember($cacheKey, 300, function () use ($year, $month, $availableYears) {
            $query = BorrowRequest::query();
            $query->whereYear('created_at', $year);

            if ($month) {
                $query->whereMonth('created_at', $month);
            }

            // Debug: Check if we have any data
            $totalRequests = BorrowRequest::count();
            \Log::info("Total BorrowRequests in database: {$totalRequests}");
            \Log::info("Filtering for year: {$year}, month: " . ($month ?: 'All'));
            
            // If no data exists, provide sample data for testing
            if ($totalRequests === 0) {
                \Log::info("No BorrowRequest data found, using sample data");
                return [
                    'borrowStatus' => [
                        'TotalRequests' => 0,
                        'checkinReq' => 0,
                        'Approved' => 0,
                        'Rejected' => 0,
                        'Pending' => 0,
                    ],
                    'chartData' => [
                        'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                        'TotalRequests' => array_fill(0, 12, 0),
                        'Approved' => array_fill(0, 12, 0),
                        'Rejected' => array_fill(0, 12, 0),
                    ],
                    'equipmentTimeData' => [
                        'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                        'datasets' => []
                    ],
                    'availableYears' => collect([now()->year]),
                    'selectedYear' => $year,
                    'selectedMonth' => $month,
                    'recentRequests' => [],
                    'categoryCounts' => Category::withCount('equipments')->get(),
                ];
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

            // Equipment time-based data for new chart
            $equipmentTimeData = [
                'labels' => [],
                'datasets' => []
            ];
            
            try {
                if ($month) {
                    // Daily data for selected month
                    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    
                    $dailyEquipmentData = BorrowRequest::with('equipment')
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->get()
                        ->groupBy(function($request) {
                            return $request->equipment->name ?? 'Unknown Item';
                        });

                    $equipmentTimeData = [
                        'labels' => array_map(fn($d) => "Day $d", range(1, $daysInMonth)),
                        'datasets' => []
                    ];

                    // Get top 5 most requested equipment
                    $topEquipment = $dailyEquipmentData->map(function($requests, $equipmentName) {
                        return [
                            'name' => $equipmentName,
                            'count' => $requests->count()
                        ];
                    })->sortByDesc('count')->take(5);

                    foreach ($topEquipment as $equipment) {
                        $equipmentName = $equipment['name'];
                        $dailyCounts = array_fill(0, $daysInMonth, 0);
                        
                        $dailyEquipmentData[$equipmentName]->groupBy(function($request) {
                            return $request->created_at->day;
                        })->each(function($dayRequests, $day) use (&$dailyCounts) {
                            $dailyCounts[$day - 1] = $dayRequests->count();
                        });

                        $equipmentTimeData['datasets'][] = [
                            'label' => $equipmentName,
                            'data' => $dailyCounts,
                            'borderColor' => $this->getRandomColor(),
                            'backgroundColor' => 'rgba(0,0,0,0)',
                            'tension' => 0.4
                        ];
                    }
                } else {
                    // Monthly data for selected year
                    $monthlyEquipmentData = BorrowRequest::with('equipment')
                        ->whereYear('created_at', $year)
                        ->get()
                        ->groupBy(function($request) {
                            return $request->equipment->name ?? 'Unknown Item';
                        });

                    $equipmentTimeData = [
                        'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                        'datasets' => []
                    ];

                    // Get top 5 most requested equipment
                    $topEquipment = $monthlyEquipmentData->map(function($requests, $equipmentName) {
                        return [
                            'name' => $equipmentName,
                            'count' => $requests->count()
                        ];
                    })->sortByDesc('count')->take(5);

                    foreach ($topEquipment as $equipment) {
                        $equipmentName = $equipment['name'];
                        $monthlyCounts = array_fill(0, 12, 0);
                        
                        $monthlyEquipmentData[$equipmentName]->groupBy(function($request) {
                            return $request->created_at->month;
                        })->each(function($monthRequests, $month) use (&$monthlyCounts) {
                            $monthlyCounts[$month - 1] = $monthRequests->count();
                        });

                        $equipmentTimeData['datasets'][] = [
                            'label' => $equipmentName,
                            'data' => $monthlyCounts,
                            'borderColor' => $this->getRandomColor(),
                            'backgroundColor' => 'rgba(0,0,0,0)',
                            'tension' => 0.4
                        ];
                    }
                }
            } catch (\Exception $e) {
                // If there's an error, provide empty data structure
                $equipmentTimeData = [
                    'labels' => [],
                    'datasets' => []
                ];
                \Log::error('Equipment chart data error: ' . $e->getMessage());
            }

            // availableYears already fetched above

            $recentRequests = (clone $query)->with('user', 'equipment')->latest()->take(5)->get()->map(function($r) {
                return [
                    'id' => $r->req_id ?? 'N/A',
                    'user_name' => $r->user->name ?? 'N/A',
                    'equipment_name' => $r->equipment->name ?? 'N/A',
                    'start' => optional($r->start_at)->format('Y-m-d'),
                    'end' => optional($r->end_at)->format('Y-m-d'),
                    'status' => ucfirst($r->status ?? 'Unknown'),
                ];
            });

            return [
                'borrowStatus' => $borrowStatus,
                'chartData' => $chartData,
                'equipmentTimeData' => $equipmentTimeData,
                'availableYears' => $availableYears,
                'selectedYear' => $year,
                'selectedMonth' => $month,
                'recentRequests' => $recentRequests,
                'categoryCounts' => Category::withCount('equipments')->get(),
            ];
        });

        // Add availableYears to the data
        $data['availableYears'] = $availableYears;

        return view('admin.index', $data);
    }

    private function getRandomColor()
    {
        $colors = [
            '#3b82f6', // Blue
            '#ef4444', // Red
            '#10b981', // Green
            '#f59e0b', // Yellow
            '#8b5cf6', // Purple
            '#06b6d4', // Cyan
            '#84cc16', // Lime
            '#f97316', // Orange
        ];
        
        return $colors[array_rand($colors)];
    }
}
