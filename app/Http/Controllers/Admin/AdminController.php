<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Equipment;
use App\Models\Category;
use App\Models\BorrowRequest;

class AdminController extends Controller
{
    public function index()
    {
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
        $statuses = ['available', 'borrowed', 'retired', 'maintenance'];

        $equipmentStatus = [];
        foreach ($statuses as $status) {
            $equipmentStatus[$status] = Equipment::where('status', $status)->count();
        }

        // Monthly chart data
        $equipmentStatusMonthly = [
            'months' => ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
        ];

        foreach ($statuses as $status) {
            $monthlyCounts = [];
            for ($month = 1; $month <= 12; $month++) {
                $monthlyCounts[] = Equipment::where('status', $status)
                    ->whereMonth('created_at', $month)
                    ->count();
            }
            $equipmentStatusMonthly[$status] = $monthlyCounts;
        }

        return view('admin.index', [
            'totalRequests'   => BorrowRequest::count(),
            'bestRatedItems'  => Equipment::latest()->take(5)->get(),
            'pendingRequests' => BorrowRequest::where('status', 'pending')->count(),
            'penaltyNotices'  => BorrowRequest::where('status', 'overdue')->count(),
            'equipmentStatus' => $equipmentStatus,
            'categoryCounts'  => Category::withCount('equipments')->get(),
            'recentRequests'  => $recentRequests,
            'equipmentStatusMonthly' => $equipmentStatusMonthly,
        ]);
    }
}
