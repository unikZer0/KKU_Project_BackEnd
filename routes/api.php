<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\Equipment;
use App\Models\Category;
use App\Models\User;
use App\Models\BorrowRequest;

Route::get('/equipments', function () {
    return Equipment::with('category')->get();
});
Route::get('/categories', function () {
    $categories = Category::withCount('equipments')->get();
    return response()->json($categories);
});
Route::get('/users', function () {
    return User::all();
});

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
