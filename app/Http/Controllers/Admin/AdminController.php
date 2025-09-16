<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Equipment;
use App\Models\Category;
use App\Models\BorrowRequest;
use Illuminate\Http\Request;

use App\Models\User;
class AdminController extends Controller
{
public function index(Request $request)
{
    $year = $request->input('year', now()->year); // default to current year

    $recentRequests = BorrowRequest::with('user', 'equipment')
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($r) {
            return [
                'id' => $r->req_id,
                'user_name' => $r->user->name ?? 'N/A',
                'equipment_name' => $r->equipment->name ?? 'N/A',
                'start' => $r->start_at->format('Y-m-d'),
                'end' => $r->end_at->format('Y-m-d'),
                'status' => ucfirst($r->status),
            ];
        });

    // Status totals (all time)
    $borrowStatus = [
        'TotalRequests' => BorrowRequest::count(),
        'Approved' => BorrowRequest::where('status', 'approved')->count(),
        'Rejected' => BorrowRequest::where('status', 'rejected')->count(),
        'Pending' => BorrowRequest::where('status', 'pending')->count(),
    ];

    // Monthly counts (for selected year)
    $months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
    $borrowStatusMonthly = [
        'months' => $months,
        'TotalRequests' => [],
        'Approved' => [],
        'Rejected' => [],
    ];

    for ($month = 1; $month <= 12; $month++) {
        $borrowStatusMonthly['TotalRequests'][] = BorrowRequest::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)->count();

        $borrowStatusMonthly['Approved'][] = BorrowRequest::where('status', 'approved')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)->count();

        $borrowStatusMonthly['Rejected'][] = BorrowRequest::where('status', 'rejected')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)->count();
    }

    // Available years for dropdown
    $availableYears = BorrowRequest::selectRaw('YEAR(created_at) as year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year');

    return view('admin.index', [
        'totalRequests' => $borrowStatus['TotalRequests'],
        'pendingRequests' => $borrowStatus['Pending'],
        'penaltyNotices' => BorrowRequest::where('status', 'overdue')->count(),
        'borrowStatus' => $borrowStatus,
        'borrowStatusMonthly' => $borrowStatusMonthly,
        'recentRequests' => $recentRequests,
        'categoryCounts' => Category::withCount('equipments')->get(),
        'selectedYear' => $year,
        'availableYears' => $availableYears,
    ]);
}
// User Report
public function userReport()
{
    $users = User::all()->map(function ($user) {
        return [
            'id' => $user->id,
                'username' => $user->name,
                'phonenumber' => $user->phonenumber ?? '-',
                'created_at' => optional($user->created_at)->format('d/m/Y'),
            ];
        });

        return view('admin.report.users', compact('users'));
    }

    public function equipmentReport()
    {
        $equipments = Equipment::with('category')->get()->map(function ($eq) {
            return [
                'id' => $eq->id,
                'name' => $eq->name,
                'category_name' => $eq->category->name ?? 'N/A',
                'created_at' => optional($eq->created_at)->format('d/m/Y'),
            ];
        });

        return view('admin.report.equipments', compact('equipments'));
    }

    public function categoryReport()
    {
        $categories = Category::all()->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
            ];
        });

        return view('admin.report.categories', compact('categories'));
    }

    // Request Report
    public function requestReport()
    {
        $requests = BorrowRequest::with('user', 'equipment')
            ->whereIn('status', ['approved', 'rejected', 'cancelled'])
            ->latest()
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'user_name' => $r->user->name ?? 'N/A',
                    'equipment_name' => $r->equipment->name ?? 'N/A',
                    'date' => $r->created_at->format('Y-m-d'),
                    'status' => ucfirst($r->status),
                    'reason' => $r->reject_reason ?? $r->cancel_reason ?? '-'
                ];
            });

        return view('admin.report.index', compact('requests'));
    }
}
