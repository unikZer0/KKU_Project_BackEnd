<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Models\BorrowTransaction;
use Illuminate\Http\Request;
use App\Notifications\BorrowRequestApproved;
use App\Notifications\BorrowRequestRejected;
use Illuminate\Support\Carbon;

class BorrowRequestController extends Controller
{
    // List pending requests
    public function index()
    {
        $requests = BorrowRequest::with('user', 'equipment')
        ->whereNotIn('status', ['check_in', 'rejected'])
            ->latest()
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'req_id' => $r->req_id,
                    'uid' => $r->user->uid,
                    'user_name' => $r->user->name ?? 'N/A',
                    'equipment_name' => $r->equipment->name ?? 'N/A',
                    'equipment_photo' => $r->equipment->photo_path ?? null,
                    'start_at' => $r->start_at ? $r->start_at->format('Y-m-d') : '-',
                    'end_at' => $r->end_at ? $r->end_at->format('Y-m-d') : '-',
                    'date' => $r->created_at->format('Y-m-d'),
                    'status' => ucfirst($r->status),
                    'reason' => $r->reject_reason ?? $r->cancel_reason ?? '-',
                ];
            });

        return view('admin.request.index', compact('requests'));
    }

    public function show($req_id)
    {
        $requests = BorrowRequest::with(['user', 'equipment', 'transaction'])
            ->where('req_id', $req_id)
            ->firstOrFail();
        $tableRequests = BorrowRequest::with('user', 'equipment')
            ->latest()
            ->take(25)
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'req_id' => $r->req_id,
                    'uid' => $r->user->uid ?? null,
                    'user_name' => $r->user->name ?? 'N/A',
                    'equipment_name' => $r->equipment->name ?? 'N/A',
                    'equipment_photo' => $r->equipment->photo_path ?? null,
                    'start_at' => $r->start_at ? $r->start_at->format('Y-m-d') : '-',
                    'end_at' => $r->end_at ? $r->end_at->format('Y-m-d') : '-',
                    'date' => $r->created_at->format('Y-m-d'),
                    'status' => ucfirst($r->status),
                    'reason' => $r->reject_reason ?? $r->cancel_reason ?? '-',
                ];
            });

        return view('admin.request.show', [
            'requests' => $requests,
            'tableRequests' => $tableRequests,
        ]);
    }
    public function approve(Request $req, $req_id)
    {
        $borrowRequest = BorrowRequest::with('transaction', 'user', 'equipment')
            ->where('req_id', $req_id)
            ->firstOrFail();
        $validated = $req->validate([
            'start_at' => ['nullable','date'],
            'end_at' => ['nullable','date','after_or_equal:start_at'],
        ]);

        if (array_key_exists('start_at', $validated)) {
            $borrowRequest->start_at = $validated['start_at'];
        }
        if (array_key_exists('end_at', $validated)) {
            $borrowRequest->end_at = $validated['end_at'];
        }
        $borrowRequest->status = 'approved';
        $borrowRequest->save();

        $user = $borrowRequest->user;
        if ($user) {
            $user->notify(new BorrowRequestApproved($borrowRequest));
        }

        return redirect()->back()
            ->with('success', 'Request approved. Allowed dates saved.');
    }

    public function reject(Request $req, $req_id)
    {
        $request = BorrowRequest::where('req_id', $req_id)->firstOrFail();
        $request->status = 'rejected';
        $request->reject_reason = $req->input('reason');
        $request->save();

        $user = $request->user;
        if ($user) {
            $user->notify(new BorrowRequestRejected($request));
        }

        return redirect()->route('admin.requests.index')
            ->with('success', 'Request rejected successfully.');
    }
    public function update(Request $req, $req_id)
    {
        $borrowRequest = BorrowRequest::with('transaction')->where('req_id', $req_id)->firstOrFail();

        $validated = $req->validate([
            'checked_out_at' => ['nullable','date_format:Y-m-d\TH:i'],
            'checked_in_at' => ['nullable','date_format:Y-m-d\TH:i','after_or_equal:checked_out_at'],
            'penalty_amount' => ['nullable','numeric','min:0'],
            'notes' => ['nullable','string','max:1000'],
        ]);

        if (!in_array($borrowRequest->status, ['approved', 'check_out'])) {
            return back()->withErrors(['status' => 'ต้องอนุมัติหรือเช็คเอาท์แล้วจึงจะสามารถบันทึกเวลาเช็คอินได้'])->withInput();
        }

        $transaction = $borrowRequest->transaction;
        if (!$transaction) {
            $transaction = new BorrowTransaction();
            $transaction->borrow_requests_id = $borrowRequest->id;
        }

        $checkedOut = $validated['checked_out_at'] ?? null;
        $checkedIn = $validated['checked_in_at'] ?? null;

        $transaction->checked_out_at = $checkedOut ? Carbon::createFromFormat('Y-m-d\TH:i', $checkedOut) : $transaction->checked_out_at;
        $transaction->checked_in_at = $checkedIn ? Carbon::createFromFormat('Y-m-d\TH:i', $checkedIn) : $transaction->checked_in_at;
        if (array_key_exists('penalty_amount', $validated)) {
            $transaction->penalty_amount = $validated['penalty_amount'];
        }
        if (array_key_exists('notes', $validated)) {
            $transaction->notes = $validated['notes'];
        }
        $transaction->save();

        $hasCheckedIn = !is_null($transaction->checked_in_at);
        $hasCheckedOut = !is_null($transaction->checked_out_at);

        if ($hasCheckedIn) {
            $borrowRequest->status = 'check_in';
            $borrowRequest->save();
        } elseif ($hasCheckedOut) {
            $borrowRequest->status = 'check_out';
            $borrowRequest->save();
        }

        return redirect()->route('admin.requests.index')->with('success', 'บันทึกเวลาเช็คเอาท์/เช็คอินเรียบร้อย');
    }
}
