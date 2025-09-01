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

    // Example monthly data for chart
    $equipmentStatusMonthly = [
        'available'   => [10, 12, 15, 11, 14, 16, 18, 20, 19, 17, 15, 12],
        'borrowed'    => [5, 3, 2, 4, 3, 2, 1, 2, 3, 4, 5, 6],
        'maintenance' => [1, 2, 1, 2, 1, 1, 2, 1, 1, 2, 1, 1],
        'months'      => ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]
    ];

    return view('admin.index', [
        'totalRequests'   => BorrowRequest::count(),
        'bestRatedItems'  => Equipment::latest()->take(5)->get(),
        'pendingRequests' => BorrowRequest::where('status', 'pending')->count(),
        'penaltyNotices'  => BorrowRequest::where('status', 'overdue')->count(),
        'equipmentStatus' => [
            'available'   => Equipment::where('status', 'available')->count(),
            'borrowed'    => Equipment::where('status', 'borrowed')->count(),
            'maintenance' => Equipment::where('status', 'maintenance')->count(),
        ],
        'categoryCounts'       => Category::withCount('equipments')->get(),
        'recentRequests'       => $recentRequests,
        'equipmentStatusMonthly' => $equipmentStatusMonthly, // <- Add this
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