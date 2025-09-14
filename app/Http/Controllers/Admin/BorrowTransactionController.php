<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\BorrowRequest;
use App\Models\BorrowTransaction;

class BorrowTransactionController extends Controller
{
    public function index()
    {
        // Get all borrow requests with their transaction
        $requests = BorrowRequest::with('transaction')->latest()->get();
        return view('admin.transaction.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipments_id' => 'required|exists:equipments,id',
            'end_at'        => 'required|date|after:today',
        ]);

        // Create borrow request
        $borrowRequest = BorrowRequest::create([
            'users_id'      => auth()->id(),
            'equipments_id' => $validated['equipments_id'],
            'start_at'      => now(),
            'end_at'        => $validated['end_at'],
            'status'        => 'pending',
            'req_id'        => 'REQ' . strtoupper(Str::random(10)),
        ]);

        // Create associated transaction
        $borrowRequest->transaction()->create([
            'penalty_amount' => 0,
        ]);

        return redirect()
            ->route('admin.transaction.index')
            ->with('success', 'Borrow Request created with Transaction!');
    }

    public function checkout($id)
    {
        $transaction = BorrowTransaction::with('borrowRequest')->findOrFail($id);
        $transaction->checked_out_at = now();
        $transaction->save();

        return redirect()->route('admin.transaction.index')
            ->with('success', 'Checked out successfully!');
    }

    public function checkin($id)
    {
        $transaction = BorrowTransaction::with('borrowRequest')->findOrFail($id);
        $transaction->checked_in_at = now();

        $endAt = optional($transaction->borrowRequest)->end_at;
        $checkInAt = $transaction->checked_in_at;

        $lateDays = 0;

        if ($endAt && $checkInAt && $checkInAt->greaterThan($endAt)) {
            $secondsLate = $checkInAt->timestamp - $endAt->timestamp;

            $lateDays = ceil($secondsLate / 86400);
        }

        $transaction->penalty_amount = $lateDays * 50;
        $transaction->save();

        return redirect()->route('admin.transaction.index')
            ->with('success', 'Checked in successfully!');
    }
}
