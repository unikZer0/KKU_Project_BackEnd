<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use Illuminate\Http\Request;

class BorrowRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'borrow_date'  => 'required|date',
            'return_date'  => 'required|date|after:borrow_date',
        ]);

        $borrowRequest = BorrowRequest::create([
            'user_id'      => auth()->id(),
            'equipment_id' => $request->equipment_id,
            'borrow_date'  => $request->borrow_date,
            'return_date'  => $request->return_date,
            'status'       => 'pending',
        ]);

        return response()->json($borrowRequest, 201);
    }

    public function approve($id)
    {
        $borrow = BorrowRequest::findOrFail($id);
        $borrow->status = 'approved';
        $borrow->save();

        return response()->json(['message' => 'Request approved']);
    }

    public function reject($id)
    {
        $borrow = BorrowRequest::findOrFail($id);
        $borrow->status = 'rejected';
        $borrow->save();

        return response()->json(['message' => 'Request rejected']);
    }

    public function checkout($id)
    {
        $borrow = BorrowRequest::findOrFail($id);
        $borrow->status = 'checked_out';
        $borrow->save();

        return response()->json(['message' => 'Equipment checked out']);
    }

    public function checkin($id)
    {
        $borrow = BorrowRequest::findOrFail($id);
        $borrow->status = 'checked_in';
        $borrow->save();

        return response()->json(['message' => 'Equipment checked in']);
    }
}
