<?php

namespace App\Http\Controllers\Borrowers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BorrowRequest;
use App\Models\BorrowRequestItem;
use Illuminate\Support\Facades\Auth;
use App\Notifications\BorrowRequestCreated;
use App\Models\EquipmentAccessory;
use App\Models\Equipment;
use App\Models\User;
use Carbon\Carbon;
use App\Models\BorrowRequestAccessory;
use Illuminate\Support\Facades\Cache;
use App\Models\EquipmentSpecification;

class BorrowerCtrl extends Controller
{
   public function show($code)
{
    // Use a cached query for performance on this complex page
    $cacheKey = "equipment_details:{$code}";
    $equipment = Cache::remember($cacheKey, 300, function () use ($code) {
        return Equipment::where('code', $code)
            ->with('category')
            ->withCount([
                'items as items_count',
                'items as available_items_count' => fn ($q) => $q->where('status', 'available')
            ])
            ->firstOrFail();
    });

    // Check if the current user has an active request for this equipment
    $hasBorrowed = Auth::check() ? BorrowRequest::where('users_id', Auth::id())
        ->where('equipment_id', $equipment->id)
        ->whereIn('status', ['pending', 'approved', 'check_out'])
        ->exists() : false;

    // --- Calendar & Availability Calculation ---
    $rangeStart = Carbon::today();
    $rangeEnd = Carbon::today()->copy()->addMonths(3);
    $totalUnits = $equipment->items_count;

    // Get all individual item bookings that are active within our 3-month window
    $activeItems = BorrowRequestItem::whereHas('request', function ($q) use ($equipment, $rangeStart, $rangeEnd) {
        $q->where('equipment_id', $equipment->id)
            ->whereIn('status', ['pending', 'approved', 'check_out'])
            ->where(fn ($query) => $query->where('start_at', '<=', $rangeEnd)->where('end_at', '>=', $rangeStart));
    })->with('request:id,start_at,end_at')->get();

    // Fetch accessories available to be requested
    $accessories = ($equipment->available_items_count > 0) ? $equipment->accessories()->whereNull('equipment_item_id')->orderBy('name')->get() : collect();
    $itemAccessories = $equipment->accessories()->whereNotNull('equipment_item_id')->whereHas('equipmentItem', fn($q) => $q->where('status', 'available'))->orderBy('equipment_item_id')->get();
    $itemSerials = $equipment->items()->where('status', 'available')->pluck('serial_number', 'id');
    
    // Fetch specific specs for the info box
    $specs = EquipmentSpecification::where('equipment_id', $equipment->id)
        ->whereIn('spec_key', ['sensor', 'megapixels', 'wifi'])
        ->get()->keyBy('spec_key');

    // $userVerified = Auth::check() ? (int)Auth::user()->is_verified : null;
    $userVerified = Auth::check() ;

    // Calculate earliest available date when equipment will be free
    $earliestAvailableDate = $this->calculateEarliestAvailableDate($equipment, $activeItems, $totalUnits);
    
    // Calculate borrowed count
    $borrowedCount = $totalUnits - $equipment->available_items_count;

    return view('equipments.show', compact(
        'equipment', 'hasBorrowed', 'userVerified', 'accessories', 
        'itemAccessories', 'itemSerials', 'specs', 'earliestAvailableDate', 'borrowedCount', 'activeItems', 'totalUnits'
    ));
}
    public function myRequests(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อนทำการยืม');
        }

        $start_at = $request->start_at ? Carbon::createFromFormat('d/m/Y', $request->start_at)->format('Y-m-d') : null;
        $end_at = $request->end_at ? Carbon::createFromFormat('d/m/Y', $request->end_at)->format('Y-m-d') : null;

        $request->merge([
            'start_at' => $start_at,
            'end_at' => $end_at,
        ]);

        $maxDate = Carbon::today()->addMonths(3)->format('Y-m-d');
        
        $validated = $request->validate([
            'start_at' => 'required|date|after_or_equal:today|before_or_equal:' . $maxDate,
            'end_at' => 'required|date|after:start_at|before_or_equal:' . $maxDate,
            'equipments_id' => 'required|exists:equipments,id',
            'request_reason' => 'required|in:assignment,personal,other',
            'request_reason_other' => 'required_if:request_reason,other|nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'extra_accessories' => 'nullable|array',
            'extra_accessories.*' => 'exists:equipment_accessories,id',
        ]);

        $equipment = Equipment::findOrFail($validated['equipments_id']);

        $conflictingBookingsCount = BorrowRequestItem::whereHas('request', function ($q) use ($validated, $equipment) {
                $q->whereIn('status', ['pending', 'approved', 'check_out'])
                  ->where('equipment_id', $equipment->id)
                  ->where(function ($query) use ($validated) {
                      $query->whereBetween('start_at', [$validated['start_at'], $validated['end_at']])
                            ->orWhereBetween('end_at', [$validated['start_at'], $validated['end_at']])
                            ->orWhere(function ($sub) use ($validated) {
                                $sub->where('start_at', '<', $validated['start_at'])
                                    ->where('end_at', '>', $validated['end_at']);
                            });
                  });
            })
            ->count();
        
        $totalUnits = $equipment->items()->count();
        
        if (($totalUnits - $conflictingBookingsCount) < $validated['quantity']) {
            return redirect()->back()->with('error', 'อุปกรณ์ไม่เพียงพอสำหรับช่วงวันที่และจำนวนที่เลือก');
        }

        $occupiedItemIds = BorrowRequestItem::whereHas('request', function ($q) use ($validated, $equipment) {
                $q->whereIn('status', ['pending', 'approved', 'check_out'])
                  ->where('equipment_id', $equipment->id)
                  ->where(function ($query) use ($validated) {
                      $query->whereBetween('start_at', [$validated['start_at'], $validated['end_at']])
                            ->orWhereBetween('end_at', [$validated['start_at'], $validated['end_at']])
                            ->orWhere(function ($sub) use ($validated) {
                                $sub->where('start_at', '<', $validated['start_at'])
                                    ->where('end_at', '>', $validated['end_at']);
                            });
                  });
            })
            ->pluck('equipment_item_id')
            ->toArray();

        $availableItems = $equipment->items()
            ->whereNotIn('id', $occupiedItemIds)
            ->limit($validated['quantity'])
            ->get();
        
        $borrowRequest = new BorrowRequest();
        $borrowRequest->users_id = Auth::id();
        $borrowRequest->equipment_id = $equipment->id;
        $borrowRequest->start_at = $validated['start_at'];
        $borrowRequest->end_at = $validated['end_at'];
        $borrowRequest->status = 'pending';
        $borrowRequest->request_reason = $validated['request_reason'] === 'other'
            ? ($validated['request_reason_other'] ?? 'other')
            : $validated['request_reason'];
        $borrowRequest->save();
        foreach ($availableItems as $item) {
            $createdItem = BorrowRequestItem::create([
                'borrow_request_id' => $borrowRequest->id,
                'equipment_item_id' => $item->id,
                'condition_out' => $item->condition,
            ]);

            $item->update(['status' => 'unavailable']);
            $itemAccs = EquipmentAccessory::where('equipment_item_id', $item->id)->get();
            foreach ($itemAccs as $acc) {
                BorrowRequestAccessory::create([
                    'borrow_request_item_id' => $createdItem->id,
                    'accessory_id' => $acc->id,
                    'condition_out' => $acc->condition,
                ]);
                $acc->update(['status' => 'unavailable']);
            }
        }

        if (!empty($validated['extra_accessories']) && count($validated['extra_accessories']) > 0) {
            foreach ($validated['extra_accessories'] as $accId) {
                $acc = EquipmentAccessory::find($accId);

                BorrowRequestAccessory::create([
                    'borrow_request_item_id' => $createdItem->id,
                    'accessory_id' => $accId,
                    'condition_out' => 'Good',
                ]);
                if ($acc) {
                    $acc->update(['status' => 'unavailable']);
                }
            }
        }


        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new BorrowRequestCreated($borrowRequest));
        }
        
        Cache::forget("myreq:" . Auth::id());
        Cache::forget("equipment_details:{$equipment->code}");

        return redirect()->route('borrower.equipments.myreq')
            ->with('success', 'ส่งคำขอยืมสำเร็จแล้ว');
    }


    public function myreq()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบ');
        }

        $userId = Auth::id();
        $cacheKey = "myreq:{$userId}";

        $requests = Cache::remember($cacheKey, 600, function () use ($userId) {
            return BorrowRequest::with([
                    'items.equipmentItem.equipmentType.category',
                    'user:id,uid,name,email,phonenumber'
                ])
                ->where('users_id', $userId)
                ->latest()
                ->get();
        });

        return view('equipments.myreq', compact('requests'));
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancel_reason' => 'required|string|max:255',
        ]);

        $req = BorrowRequest::where('id', $id)->where('users_id', Auth::id())->firstOrFail();
        
        if (!in_array($req->status, ['pending', 'approved'])) {
            return redirect()->back()->with('error', 'ไม่สามารถยกเลิกคำขอในสถานะนี้ได้');
        }

        $req->status = 'cancelled';
        $req->cancel_reason = $request->cancel_reason;
        $req->save();

        Cache::forget("myreq:{$req->users_id}");
        Cache::forget("reqdetail:{$req->req_id}");

        return redirect()->back()->with('success', 'คำขอถูกยกเลิกแล้ว');
    }
    
    public function search(Request $request)
    {
        try {
            $q = $request->query('q');
            $equipments = Equipment::with('category')
                ->when($q, function ($query) use ($q) {
                    $query->where(function ($subQuery) use ($q) {
                        $subQuery->where('name', 'like', "%$q%")
                            ->orWhere('code', 'like', "%$q%")
                            ->orWhere('status', 'like', "%$q%")
                            ->orWhereHas('category', function ($catQuery) use ($q) {
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
        $equipments = Cache::remember('all_equipment_list', 1800, function () { 
            return Equipment::with('category')
                ->orderBy('name')
                ->limit(50)
                ->get();
        });

        return response()->json(['data' => $equipments]);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}
    public function reqdetail($req_id)
{
    if (!Auth::check()) {
        return redirect()->back()->with('showLoginConfirm', true);
    }
    $reQuests = Cache::remember("reqdetail:{$req_id}", 600, function () use ($req_id) { 
        return BorrowRequest::with(
            'equipment:id,code,name,description,categories_id,photo_path',
            'user:id,uid,name,email,phonenumber',
            'equipment.category:id,name'
        )
            ->where('req_id', $req_id)
            ->get();
    });

    return view('equipments.reqdetail', compact('reQuests'));
}

/**
 * Calculate the earliest available date when equipment will be free
 */
private function calculateEarliestAvailableDate($equipment, $activeItems, $totalUnits)
{
    // If all items are available, return today
    if ($equipment->available_items_count >= $totalUnits) {
        return [
            'date' => Carbon::today()->format('Y-m-d'),
            'available_count' => $totalUnits,
            'message' => 'พร้อมให้ยืมทันที'
        ];
    }

    // Get all return dates from active borrow requests with their counts
    $returnDatesWithCounts = [];
    foreach ($activeItems as $item) {
        $endDate = Carbon::parse($item->request->end_at);
        $dateStr = $endDate->format('Y-m-d');
        
        if (!isset($returnDatesWithCounts[$dateStr])) {
            $returnDatesWithCounts[$dateStr] = 0;
        }
        $returnDatesWithCounts[$dateStr]++;
    }

    // Sort by date
    ksort($returnDatesWithCounts);
    
    // Find the earliest date when we'll have items available
    $today = Carbon::today();
    $earliestDate = null;
    $firstRequestCount = 0;
    
    foreach ($returnDatesWithCounts as $returnDate => $count) {
        $returnCarbon = Carbon::parse($returnDate);
        
        // Only consider future return dates
        if ($returnCarbon->gte($today)) {
            $earliestDate = $returnDate;
            $firstRequestCount = $count;
            break; // We found the first request that will be returned
        }
    }

    if ($earliestDate) {
            $dateShifted = Carbon::parse($earliestDate)->addDay();
    $dateFormatted = $dateShifted->format('d/m/Y');

        return [
            'date' => $earliestDate,
            'available_count' => $equipment->available_items_count + $firstRequestCount,
            'message' => "เร็วสุด: {$dateFormatted} (จะว่าง {$firstRequestCount} ชิ้น)"
        ];
    }

    // If no future return dates found, check if there are any items available now
    if ($equipment->available_items_count > 0) {
        return [
            'date' => Carbon::today()->format('Y-m-d'),
            'available_count' => $equipment->available_items_count,
            'message' => "พร้อมให้ยืม {$equipment->available_items_count} ชิ้น"
        ];
    }

    return [
        'date' => null,
        'available_count' => 0,
        'message' => 'ไม่พร้อมให้ยืมในขณะนี้'
    ];
}
}
