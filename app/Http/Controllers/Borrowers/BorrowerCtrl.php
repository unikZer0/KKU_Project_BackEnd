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
        if (in_array($equipment->status, ['maintenance'])) {
            return redirect()->back()
                ->with('error', 'อุปกรณ์นี้ไม่สามารถยืมได้ เพราะสถานะคือ ' . $equipment->status);
        }

        $start = $request->start_at;
        $end = $request->end_at;

        $overlapExists = BorrowRequest::where('equipments_id', $equipment->id)
            ->whereIn('status', ['pending', 'approved', 'check_out'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_at', [$start, $end])
                    ->orWhereBetween('end_at', [$start, $end])
                    ->orWhere(function ($query2) use ($start, $end) {
                        $query2->where('start_at', '<=', $start)
                            ->where('end_at', '>=', $end);
                    });
            })
            ->exists();

        if ($overlapExists) {
            return redirect()->back()->with('error', 'ช่วงเวลาที่เลือกถูกจองแล้ว กรุณาเลือกวันอื่น');
        }

        $borrowRequest = new BorrowRequest();
        $borrowRequest->users_id = Auth::id();
        $borrowRequest->equipments_id = $equipment->id;
        $borrowRequest->start_at = $start;
        $borrowRequest->end_at = $end;
        $borrowRequest->status = 'pending';
        $borrowRequest->save();

        return redirect()->back()
            ->with('success', 'ส่งคำขอยืมสำเร็จ');
    }

    public function show($encryptedId)
    {
        $id = decrypt($encryptedId);
        $equipment = Equipment::findOrFail($id);

        $hasBorrowed = false;
        if (Auth::check()) {
            $hasBorrowed = BorrowRequest::where('users_id', Auth::id())
                ->where('equipments_id', $equipment->id)
                ->whereIn('status', ['pending', 'approved', 'check_out'])
                ->exists();
            // dd(Auth::id(), $equipment->id, $hasBorrowed);
        }

        $bookings = BorrowRequest::where('equipments_id', $equipment->id)
            ->whereIn('status', ['pending', 'approved', 'check_out'])
            ->orderBy('start_at')
            ->get();

        return view('equipments.show', compact('equipment', 'bookings', 'hasBorrowed'));

    }

    public function myreq()
    {
        return view('equipments.myreq');
    }
}
