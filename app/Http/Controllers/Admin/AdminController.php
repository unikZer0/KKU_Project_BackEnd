<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryService;
use App\Models\Equipment;
use App\Models\Category;
use App\Models\User;
use App\Models\BorrowRequest;
use Illuminate\Http\Request;
use App\Notifications\BorrowRequestApproved;
use App\Notifications\BorrowRequestRejected;

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
