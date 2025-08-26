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
        return view('admin.index', [
            'totalRequests'   => BorrowRequest::count(),
            // Use latest added equipment instead of rating
            'bestRatedItems'  => Equipment::latest()->take(5)->get(),
            'pendingRequests' => BorrowRequest::where('status', 'pending')->count(),
            'penaltyNotices'  => BorrowRequest::where('status', 'overdue')->count(),

            // Chart data
            'equipmentStatus' => [
                'available'   => Equipment::where('status', 'available')->count(),
                'borrowed'    => Equipment::where('status', 'borrowed')->count(), // fixed status name
                'maintenance' => Equipment::where('status', 'maintenance')->count(),
            ],

            'categoryCounts' => Category::withCount('equipments')->get(),

            // Recent activity
            'recentRequests' => BorrowRequest::latest()->take(5)->get(),
        ]);
    }
}
