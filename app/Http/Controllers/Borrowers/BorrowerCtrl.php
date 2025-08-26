<?php

namespace App\Http\Controllers\Borrowers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BorrowRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Categories;

use App\Models\Equipment;
use App\Models\Category;

class BorrowerCtrl extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->q) {
            $query->where('name', 'like', "%{$request->q}%");
        }

        $equipments = $query->paginate(10);

        return response()->json($equipments);
    }
    //request borrower
    public function myRequests(Request $request)
{
    if (!Auth::check()) {
        return redirect()->back()->with('showLoginConfirm', true);
    }

    $request->validate([
        'start_at' => 'required|date',
        'end_at' => 'required|date|after:start_at',
        'equipments_id' => 'required|exists:equipments,id',
    ]);

    $equipment = Equipment::findOrFail($request->equipments_id);
    if ($equipment->status !== 'available') {
        return redirect()->back()
            ->with('error', 'อุปกรณ์นี้ไม่สามารถยืมได้ เพราะสถานะคือ ' . $equipment->status);
    }

    $borrowRequest = new BorrowRequest();
    $borrowRequest->users_id = Auth::id();
    $borrowRequest->equipments_id = $request->equipments_id;
    $borrowRequest->start_at = $request->start_at;
    $borrowRequest->end_at = $request->end_at;
    $borrowRequest->status = 'pending';
    $borrowRequest->save();

    $equipment->status = 'unavailable';
    $equipment->save();

    return redirect()->back()
        ->with('success', 'Your borrow request has been submitted successfully.');
}

    public function show($encryptedId)
{
    $id = decrypt($encryptedId);
    $equipment = Equipment::findOrFail($id);

    $hasBorrowed = false;
    if (Auth::check()) {
        $hasBorrowed = BorrowRequest::where('users_id', Auth::id())
            ->where('equipments_id', $equipment->id)
            ->whereIn('status', ['pending','approved','check_out'])
            ->exists();
            // dd(Auth::id(), $equipment->id, $hasBorrowed);
    }

    return view('equipments.show', compact('equipment', 'hasBorrowed'));
}

    public function myreq (){
        return view('equipments.myreq');
    }
}
