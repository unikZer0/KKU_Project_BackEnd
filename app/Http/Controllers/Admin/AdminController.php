<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryService;
use App\Models\Equipment;
use App\Models\Category;
use App\Models\User;
use App\Models\BorrowRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
{
    // Recent requests (last 5)
    $recentRequests = BorrowRequest::with('user', 'equipment')
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($r) {
            return [
                'id' => $r->id,
                'user_name' => $r->user->username ?? 'N/A',
                'equipment_name' => $r->equipment->name ?? 'N/A',
                'date' => $r->created_at->format('Y-m-d'),
                'status' => ucfirst($r->status),
            ];
        });

    // Get all possible statuses from the database dynamically
    $statuses = ['available', 'borrowed', 'retired', 'maintenance']; // Add more if needed

    // Count equipment per status
    $equipmentStatus = [];
    foreach ($statuses as $status) {
        $equipmentStatus[$status] = Equipment::where('status', $status)->count();
    }

    // Monthly chart data
    $equipmentStatusMonthly = [
        'months' => ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]
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

public function requestIndex(CloudinaryService $cloudinary)
{
    $requests = BorrowRequest::with('user', 'equipment')
        ->where('status', 'pending') // only pending requests
        ->latest()
        ->get()
        ->map(function ($r) {
            return [
                'id' => $r->id,
                'user_name' => $r->user->username ?? 'N/A',
                'equipment_name' => $r->equipment->name ?? 'N/A',
                'equipment_photo' => $r->equipment->photo_path ?? null,
                'start_at' => $r->start_at ? $r->start_at->format('Y-m-d') : '-',
                'end_at' => $r->end_at ? $r->end_at->format('Y-m-d') : '-',
                'date' => $r->created_at->format('Y-m-d'),
                'status' => ucfirst($r->status),
                'reason' => $r->reject_reason ?? $r->cancel_reason ?? '-',
            ];
        });

    return view('admin.request.index', [
        'requests' => $requests
    ]);
}

//Approve Request
public function approveRequest($id)
{
    $request = BorrowRequest::findOrFail($id);
    $request->status = 'approved';
    $request->save();

    return redirect()->route('admin.requests.index')->with('success', 'Request approved successfully.');
}

//Reject Request
public function rejectRequest(Request $req, $id)
{
    $request = BorrowRequest::findOrFail($id);
    $request->status = 'rejected';
    $request->reject_reason = $req->input('reason');
    $request->save();

    return redirect()->route('admin.requests.index')->with('success', 'Request rejected successfully.');
}

//Request Report
public function requestReport()
{
    // Only get requests with status = approved, rejected, or cancelled
    $requests = BorrowRequest::with('user', 'equipment')
        ->whereIn('status', ['approved', 'rejected', 'cancelled'])
        ->latest()
        ->get()
        ->map(function ($r) {
            return [
                'id' => $r->id,
                'user_name' => $r->user->username ?? 'N/A',
                'equipment_name' => $r->equipment->name ?? 'N/A',
                'date' => $r->created_at->format('Y-m-d'),
                'status' => ucfirst($r->status),
                'reason' => $r->reject_reason ?? $r->cancel_reason ?? '-'
            ];
        });

    return view('admin.request.report', [
        'requests' => $requests
    ]);
}

}