<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;

use App\Models\Equipment;
use App\Models\Category;
use App\Models\User;
use App\Models\BorrowRequest;
use App\Models\Log;

// EQUIPMENTS
Route::get('/equipments', function () {
    return Equipment::with('category')->get();
});
// CATEGORIES
Route::get('/categories', function () {
    $categories = Category::withCount('equipments')->get();
    return response()->json($categories);
});
// USERS
Route::get('/users', function () {
    return User::all();
});

// REQUESTS
route::get('/requests', function () {
    return BorrowRequest::with('user', 'equipment')->get()->map(function ($req) {
        return [
            'id' => $req->id,
            'req_id' => $req->req_id,
            'user_name' => $req->user->name ?? 'N/A',
            'equipment_name' => $req->equipment->name ?? 'N/A',
            'start_at' => $req->start_at ? $req->start_at->format('Y-m-d') : '-',
            'end_at' => $req->end_at ? $req->end_at->format('Y-m-d') : '-',
            'status' => $req->status,
            'eject_reason' => $req->eject_reason ?? '-',
            'reason' => $req->reject_reason ?? $req->cancel_reason ?? '-',
            'created_at' => optional($req->created_at)->format('Y-m-d'),
        ];
    });
});

// Logs
Route::get('/logs', function (Request $request) {
    $query = Log::with('user');
    
    // Apply filters
    if ($request->filled('admin')) {
        $query->whereHas('user', function($q) use ($request) {
            $q->where('name', 'like', '%' . $request->admin . '%');
        });
    }
    
    if ($request->filled('action')) {
        $query->where('action', 'like', '%' . $request->action . '%');
    }
    
    // Paginate results
    $perPage = 10;
    $logs = $query->orderBy('created_at', 'desc')->paginate($perPage);
    
    return response()->json([
        'data' => $logs->items(),
        'current_page' => $logs->currentPage(),
        'last_page' => $logs->lastPage(),
        'total' => $logs->total(),
    ]);
});

// Notification API routes - all using web authentication
Route::middleware('web')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/mark-read/{id}', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});
