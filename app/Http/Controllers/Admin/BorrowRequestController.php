<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use Illuminate\Http\Request;
use App\Notifications\BorrowRequestApproved;
use App\Notifications\BorrowRequestRejected;

class BorrowRequestController extends Controller
{
    // List pending requests
    public function index()
    {
        $requests = BorrowRequest::with('user', 'equipment')
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
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

    public function show($id)
    {
        $requests = \App\Models\BorrowRequest::with(['user', 'equipment'])->findOrFail($id);
        return view('admin.request.show', compact('requests'));
    }

    // Approve request
    public function approve($id)
    {
        $request = BorrowRequest::findOrFail($id);
        $request->status = 'approved';
        $request->save();

        $user = $request->user;
        if ($user) {
            $user->notify(new BorrowRequestApproved($request));
        }

        return redirect()->route('admin.requests.index')
            ->with('success', 'Request approved successfully.');
    }

    // Reject request
    public function reject(Request $req, $id)
    {
        $request = BorrowRequest::findOrFail($id);
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
}
