<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsActivity;
use App\Models\BorrowRequest;
use App\Models\BorrowTransaction;
use App\Models\EquipmentItem;
use App\Models\EquipmentAccessory;
use Illuminate\Http\Request;
use App\Notifications\BorrowRequestApproved;
use App\Notifications\BorrowRequestRejected;
use App\Notifications\BorrowRequestCheckedOut;
use App\Notifications\BorrowRequestCheckedIn;
use App\Services\LateReturnService;
use Illuminate\Support\Carbon;
use App\Traits\ClearsDashboardCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BorrowRequestController extends Controller
{
    use ClearsDashboardCache, LogsActivity;

    public function index()
    {
        $requests = BorrowRequest::with(['user', 'equipment'])
            ->latest()
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

        return view('admin.request.index', compact('requests'));
    }

    public function show($req_id)
    {
        $requests = BorrowRequest::with([
            'user', 
            'equipment.category', 
            'transaction',
            'items.equipmentItem',
            'items.accessories.accessory'
        ])
            ->where('req_id', $req_id)
            ->firstOrFail();

        $tableRequests = BorrowRequest::with(['user', 'equipment'])
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
        $borrowRequest = BorrowRequest::with(['transaction', 'user', 'equipment'])
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
        $borrowRequest->pickup_deadline = now()->addDays(3);
        $borrowRequest->save();
        
        // Log approval
        $this->logBorrowRequest('approve', $borrowRequest, [
            'description' => "อนุมัติคำขอยืม {$borrowRequest->req_id} สำหรับผู้ใช้ {$borrowRequest->user->name} เรียบร้อย",
            'severity' => 'info'
        ]);
        
        $user = $borrowRequest->user;
        if ($user) {
            $user->notify(new BorrowRequestApproved($borrowRequest));
        }

        // Clear all related caches
        Cache::forget("myreq:{$borrowRequest->users_id}");
        Cache::forget("reqdetail:{$borrowRequest->req_id}");
        Cache::forget("reqdetail:{$borrowRequest->req_id}:v3");
        Cache::forget("equipment_details:{$borrowRequest->equipment->code}");
        Cache::forget("all_equipment_list"); // Clear equipment list cache
        Cache::flush(); // Clear all cache to ensure fresh data

        return redirect()->back()->with('success', 'อนุมัติคำขอยืมอุปกรณ์เรียบร้อยแล้ว');
    }

    public function reject(Request $req, $req_id)
    {
        $request = BorrowRequest::with('equipment')->where('req_id', $req_id)->firstOrFail();
        $request->status = 'rejected';
        $request->reject_reason = $req->input('reason');
        $request->save();

        // Log rejection
        $this->logBorrowRequest('reject', $request, [
            'description' => "ปฏิเสธคำขอยืม {$request->req_id} สำหรับผู้ใช้ {$request->user->name} เนื่องจาก: {$request->reject_reason}",
            'severity' => 'warning'
        ]);

        $user = $request->user;
        if ($user) {
            $user->notify(new BorrowRequestRejected($request));
        }

        Cache::forget("myreq:{$request->users_id}");
        Cache::forget("reqdetail:{$request->req_id}");
        Cache::forget("reqdetail:{$request->req_id}:v3");
        Cache::forget("equipment_details:{$request->equipment->code}");
        Cache::forget("all_equipment_list");
        Cache::flush(); 

        return redirect()->route('admin.requests.index')->with('success', 'ปฏิเสธคำขอยืมอุปกรณ์เรียบร้อยแล้ว');
    }

    public function checkout(Request $request, $req_id)
    {
        $borrowRequest = BorrowRequest::with('equipment')->where('req_id', $req_id)->firstOrFail();
        
        if ($borrowRequest->status !== 'approved') {
            return redirect()->back()->with('error', 'Cannot checkout a request that is not approved.');
        }

        if ($borrowRequest->is_checked_out) {
            return redirect()->back()->with('error', 'This request has already been checked out.');
        }

        $borrowRequest->update([
            'is_checked_out' => true,
            'checked_out_at' => now(),
            'checked_out_by' => Auth::user()->name,
        ]);

        // Log checkout process
        $this->logBorrowRequest('checkout', $borrowRequest, [
            'description' => "ส่งมอบอุปกรณ์ให้ผู้ยืมสำหรับคำขอ {$borrowRequest->req_id} โดย {$borrowRequest->checked_out_by} เรียบร้อย",
            'severity' => 'info'
        ]);

        Cache::forget("myreq:{$borrowRequest->users_id}");
        Cache::forget("reqdetail:{$borrowRequest->req_id}");
        Cache::forget("reqdetail:{$borrowRequest->req_id}:v3");
        Cache::forget("equipment_details:{$borrowRequest->equipment->code}");
        Cache::forget("all_equipment_list");
        Cache::flush(); 
        
        return redirect()->back()->with('success', 'บันทึกการมารับอุปกรณ์เรียบร้อยแล้ว');
    }

    public function update(Request $req, $req_id)
{
    $borrowRequest = BorrowRequest::with(['transaction', 'equipment', 'items.accessories'])
        ->where('req_id', $req_id)
        ->firstOrFail();

    $validated = $req->validate([
        'checked_out_at' => ['nullable','date_format:Y-m-d\TH:i'],
        'checked_in_at' => ['nullable','date_format:Y-m-d\TH:i','after_or_equal:checked_out_at'],
        'penalty_amount' => ['nullable','numeric','min:0'],
        'notes' => ['nullable','string','max:1000'],
        'item_condition_in' => ['nullable', 'array'],
        'item_condition_in.*' => ['nullable', 'string'],
        'accessory_condition_in' => ['nullable', 'array'],
        'accessory_condition_in.*' => ['nullable', 'string'],
    ]);

    // ⏰ Validate time vs request period
    if (isset($validated['checked_out_at']) && $validated['checked_out_at'] && $borrowRequest->start_at && $borrowRequest->end_at) {
        $checkedOutAt = Carbon::createFromFormat('Y-m-d\TH:i', $validated['checked_out_at']);
        $startAt = Carbon::parse($borrowRequest->start_at);
        $endAt = Carbon::parse($borrowRequest->end_at);
        
        if ($checkedOutAt->lt($startAt) || $checkedOutAt->gt($endAt)) {
            return back()->withErrors([
                'checked_out_at' => 'วันที่มาเเอาของต้องอยู่ในช่วงวันที่เริ่ม (' . $startAt->format('Y-m-d') . ') ถึงวันที่สิ้นสุด (' . $endAt->format('Y-m-d') . ')'
            ])->withInput();
        }
    }

    if (!in_array($borrowRequest->status, ['approved', 'check_out'])) {
        return back()->withErrors(['status' => 'ต้องอนุมัติหรือเช็คเอาท์แล้วจึงจะสามารถบันทึกเวลาเช็คอินได้'])->withInput();
    }

    $transaction = $borrowRequest->transaction ?? new BorrowTransaction();
    $transaction->borrow_requests_id = $borrowRequest->id;

    $checkedOut = $validated['checked_out_at'] ?? null;
    $checkedIn = $validated['checked_in_at'] ?? null;

    // If status is check_out and no checked_in_at is provided, set it to now
    if ($borrowRequest->status === 'check_out' && !$checkedIn) {
        $checkedIn = now()->format('Y-m-d\TH:i');
    }

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
            // Update equipment item conditions and status
            $itemIds = $borrowRequest->items()->whereNotNull('equipment_item_id')->pluck('equipment_item_id')->unique()->all();
            
            // Get accessory IDs from the accessories table
            $accIds = collect();
            foreach ($borrowRequest->items as $item) {
                $itemAccessoryIds = $item->accessories()->pluck('accessory_id');
                $accIds = $accIds->merge($itemAccessoryIds);
            }
            $accIds = $accIds->unique()->all();
            
            // Update equipment item conditions and status
            if (!empty($itemIds)) {
                foreach ($borrowRequest->items as $item) {
                    if ($item->equipment_item_id && isset($validated['item_condition_in'][$item->id])) {
                        // Check if any accessory for this item is marked as "หาย" (lost) or "พัง" (broken)
                        $hasLostAccessory = false;
                        $hasBrokenAccessory = false;
                        foreach ($item->accessories as $accessory) {
                            if (isset($validated['accessory_condition_in'][$accessory->id])) {
                                $accessoryCondition = $validated['accessory_condition_in'][$accessory->id];
                                if ($accessoryCondition === 'หาย') {
                                    $hasLostAccessory = true;
                                } elseif ($accessoryCondition === 'พัง') {
                                    $hasBrokenAccessory = true;
                                }
                            }
                        }
                        
                        // If any accessory is lost, automatically set main item to "อุปกรณ์ไม่พร้อมใช้งาน"
                        if ($hasLostAccessory) {
                            $item->condition_in = 'อุปกรณ์ไม่พร้อมใช้งาน';
                            $item->save();
                            
                            // Set equipment item to unavailable status
                            EquipmentItem::where('id', $item->equipment_item_id)
                                ->update([
                                    'condition' => 'อุปกรณ์ไม่พร้อมใช้งาน',
                                    'status' => 'unavailable'
                                ]);
                        } elseif ($hasBrokenAccessory) {
                            // If any accessory is broken, automatically set main item to "อุปกรณ์ไม่พร้อมใช้งาน"
                            $item->condition_in = 'อุปกรณ์ไม่พร้อมใช้งาน';
                            $item->save();
                            
                            // Set equipment item to unavailable status
                            EquipmentItem::where('id', $item->equipment_item_id)
                                ->update([
                                    'condition' => 'อุปกรณ์ไม่พร้อมใช้งาน',
                                    'status' => 'unavailable'
                                ]);
                        } else {
                            // Normal condition update
                            $item->condition_in = $validated['item_condition_in'][$item->id];
                            $item->save();
                            
                            // Update the equipment item condition
                            EquipmentItem::where('id', $item->equipment_item_id)
                                ->update([
                                    'condition' => $validated['item_condition_in'][$item->id],
                                    'status' => 'available'
                                ]);
                        }
                    }
                }
            }
            
            // Update accessory conditions and status
            if (!empty($accIds)) {
                foreach ($borrowRequest->items as $item) {
                    foreach ($item->accessories as $accessory) {
                        if (isset($validated['accessory_condition_in'][$accessory->id])) {
                            $accessoryCondition = $validated['accessory_condition_in'][$accessory->id];
                            
                            // Update the borrow request accessory condition
                            $accessory->condition_in = $accessoryCondition;
                            $accessory->save();
                            
                            // Handle different accessory conditions
                            if ($accessoryCondition === 'หาย') {
                                // If accessory is marked as "หาย" (lost), set status to "หาย"
                                EquipmentAccessory::where('id', $accessory->accessory_id)
                                    ->update([
                                        'condition' => 'หาย',
                                        'status' => 'หาย'
                                    ]);
                            } elseif ($accessoryCondition === 'พัง') {
                                // If accessory is marked as "พัง" (broken), set status to "หาย" (lost)
                                EquipmentAccessory::where('id', $accessory->accessory_id)
                                    ->update([
                                        'condition' => 'พัง',
                                        'status' => 'หาย'
                                    ]);
                            } else {
                                // Normal condition update
                                EquipmentAccessory::where('id', $accessory->accessory_id)
                                    ->update([
                                        'condition' => $accessoryCondition,
                                        'status' => 'available'
                                    ]);
                            }
                        }
                    }
                }
            }
            
            $borrowRequest->status = 'check_in';
            $borrowRequest->save();
        } elseif ($hasCheckedOut) {
            // Get equipment item IDs
            $itemIds = $borrowRequest->items()->whereNotNull('equipment_item_id')->pluck('equipment_item_id')->unique()->all();
            
            // Get accessory IDs from the accessories table
            $accIds = collect();
            foreach ($borrowRequest->items as $item) {
                $itemAccessoryIds = $item->accessories()->pluck('accessory_id');
                $accIds = $accIds->merge($itemAccessoryIds);
            }
            $accIds = $accIds->unique()->all();
            
            if (!empty($itemIds)) {
                EquipmentItem::whereIn('id', $itemIds)->update(['status' => 'unavailable']);
            }
            if (!empty($accIds)) {
                EquipmentAccessory::whereIn('id', $accIds)->update(['status' => 'unavailable']);
            }
            $borrowRequest->status = 'check_out';
            $borrowRequest->save();
        }
    }

    // clear caches
    Cache::forget("myreq:{$borrowRequest->users_id}");
    Cache::forget("reqdetail:{$borrowRequest->req_id}");
    Cache::forget("reqdetail:{$borrowRequest->req_id}:v3");
    Cache::forget("equipment_details:{$borrowRequest->equipment->code}");
    Cache::forget("all_equipment_list");
    Cache::flush();

    return redirect()->route('admin.requests.index')->with('success', 'บันทึกการคืนอุปกรณ์และสภาพอุปกรณ์เรียบร้อยแล้ว');
}

    /**
     * Show late returns management page
     */
    public function lateReturns()
    {
        $lateReturnService = new LateReturnService();
        
        $lateRequests = BorrowRequest::with(['user', 'equipment', 'transaction'])
            ->where('status', 'check_out')
            ->where('end_at', '<', now())
            ->whereDoesntHave('transaction', function ($query) {
                $query->whereNotNull('checked_in_at');
            })
            ->get()
            ->map(function ($request) use ($lateReturnService) {
                $daysLate = $lateReturnService->getDaysLate($request);
                $penaltyAmount = $lateReturnService->calculatePenaltyForRequest($request);
                
                return [
                    'id' => $request->id,
                    'req_id' => $request->req_id,
                    'user_name' => $request->user->name ?? 'N/A',
                    'user_email' => $request->user->email ?? 'N/A',
                    'equipment_name' => $request->equipment->name ?? 'N/A',
                    'end_at' => $request->end_at->format('d/m/Y'),
                    'days_late' => $daysLate,
                    'penalty_amount' => $penaltyAmount,
                    'current_penalty' => $request->transaction?->penalty_amount ?? 0,
                ];
            });

        return view('admin.request.late-returns', compact('lateRequests'));
    }

    /**
     * Send manual late return notification
     */
    public function sendLateNotification($req_id)
    {
        $request = BorrowRequest::where('req_id', $req_id)->firstOrFail();
        $lateReturnService = new LateReturnService();
        
        if ($lateReturnService->isLate($request)) {
            $daysLate = $lateReturnService->getDaysLate($request);
            $penaltyAmount = $lateReturnService->calculatePenaltyForRequest($request);
            
            $user = $request->user;
            if ($user) {
                $user->notify(new \App\Notifications\BorrowRequestLateReturn($request, $daysLate, $penaltyAmount));
            }
            
            return redirect()->back()->with('success', 'ส่งการแจ้งเตือนการคืนล่าช้าเรียบร้อยแล้ว');
        }
        
        return redirect()->back()->with('error', 'คำขอนี้ไม่ได้คืนล่าช้า');
    }

}
