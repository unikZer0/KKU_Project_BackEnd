<?php

namespace App\Http\Controllers\Borrowers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BorrowRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Categories;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Notifications\BorrowRequestCreated;

use App\Models\Equipment;
use App\Models\Category;
use Carbon\Carbon;
use PhpParser\Node\Stmt\TryCatch;

use function Laravel\Prompts\error;

class BorrowerCtrl extends Controller
{
    public function show($code)
    {
        $equipment = Cache::remember("equipment:$code", 600, function () use ($code) {
            return Equipment::where('code', $code)->firstOrFail();
        });

        $hasBorrowed = false;
        if (Auth::check()) {
            $hasBorrowed = BorrowRequest::where('users_id', Auth::id())
                ->where('equipments_id', $equipment->id)
                ->whereIn('status', ['pending', 'approved', 'check_out'])
                ->exists();
        }

        $bookings = BorrowRequest::where('equipments_id', $equipment->id)
            ->whereIn('status', ['pending', 'approved', 'check_out'])
            ->orderBy('start_at')
            ->get();

        $currentDate = Carbon::now()->toDateString();
        return view('equipments.show', compact('equipment', 'bookings', 'hasBorrowed', 'currentDate'));
    }

    public function myreq()
    {
        if (!Auth::check()) {
            return redirect()->back()->with('showLoginConfirm', true);
        }

        $userId = Auth::id();
        $reQuests = BorrowRequest::with(
            'equipment:id,code,name,description,categories_id,photo_path',
            'user:id,uid,username,age,email,phonenumber',
            'equipment.category:id,name'
        )
            ->where('users_id', $userId)
            ->get();

        return view('equipments.myreq', compact('reQuests'));
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancel_reason' => 'required|array|min:1',
        ]);

        $reasons = implode(', ', $request->cancel_reason);

        $req = BorrowRequest::findOrFail($id);
        $req->status = 'cancelled';
        $req->cancel_reason = $reasons;
        $req->save();

        Cache::forget("myreq:{$req->users_id}");

        return redirect()->back()->with('success', 'คำขอถูกยกเลิกแล้ว');
    }

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

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new BorrowRequestCreated($borrowRequest));
        }
            Cache::forget("myreq:" . Auth::id());
        return redirect()->back()
            ->with('success', 'ส่งคำขอยืมสำเร็จ');
    }
    public function search(Request $request)
    {
        try {
            $q = $request->query('q');
            $equipments = Equipment::with('category')
                ->when($q, function($query) use ($q) {
                    $query->where(function($subQuery) use ($q) {
                        $subQuery->where('name', 'like', "%$q%")
                            ->orWhere('code', 'like', "%$q%")
                            ->orWhere('description', 'like', "%$q%")
                            ->orWhere('status', 'like', "%$q%")
                            ->orWhereHas('category', function($catQuery) use ($q) {
                                $catQuery->where('name', 'like', "%$q%");
                            });
                    });
                })
                ->orderBy('name')
                ->limit(20)
                ->get();

            return response()->json(['data' => $equipments]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getAllEquipment()
    {
        try {
            $equipments = Equipment::with('category')
                ->orderBy('name')
                ->limit(50)
                ->get();

            return response()->json(['data' => $equipments]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}


