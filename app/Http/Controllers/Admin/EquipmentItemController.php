<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EquipmentItem;
use App\Models\Equipment;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class EquipmentItemController extends Controller
{
    /**
     * Display a listing of equipment items
     */
    public function index()
    {
        $equipmentItems = Cache::remember('equipment_items_with_relations', 600, function () {
            return EquipmentItem::with(['equipment.category', 'accessories'])
                ->orderBy('created_at', 'desc')
                ->get();
        });

        $equipments = Cache::remember('equipments_for_items', 600, function () {
            return Equipment::with('category')->get();
        });

        $categories = Cache::remember('all_categories', 600, function () {
            return Category::all();
        });

        return view('admin.equipment-items.index', [
            'equipmentItems' => $equipmentItems,
            'equipments' => $equipments,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new equipment item
     */
    public function create()
    {
        $equipments = Equipment::with('category')->get();
        return view('admin.equipment-items.create', compact('equipments'));
    }

    /**
     * Store a newly created equipment item
     */
    public function store(Request $request)
    {
        $rules = [
            'equipment_id'   => 'required|integer|exists:equipments,id',
            'condition'      => 'required|string|in:สภาพดี,สามารถซ่อมได้,ไม่สามารถซ่อมได้,พัง,อุปกรณ์ไม่พร้อมใช้งาน',
            'status'         => 'required|string|in:available,unavailable,maintenance,retired',
            'notes'          => 'nullable|string|max:500',
            'serial_number'  => 'nullable|string|max:255|unique:equipment_items,serial_number',
        ];

        $data = $request->validate($rules);

        // Normalize serial number (trim spaces)
        if (isset($data['serial_number'])) {
            $data['serial_number'] = trim($data['serial_number']);
        }

        $equipmentItem = EquipmentItem::create($data);

        Cache::forget('equipment_items_with_relations');

        return response()->json([
            'success' => true,
            'message' => 'Equipment item created successfully',
            'data'    => $equipmentItem->load(['equipment.category', 'accessories'])
        ]);
    }

    /**
     * Display the specified equipment item
     */
    public function show(EquipmentItem $equipmentItem)
    {
        $equipmentItem->load(['equipment.category', 'accessories']);
        return response()->json([
            'success' => true,
            'data'    => $equipmentItem
        ]);
    }

    /**
     * Update the specified equipment item
     */
    
    public function update(Request $request, EquipmentItem $equipmentItem)
    {
        $rules = [
            'equipment_id' => 'required|integer|exists:equipments,id',
            'condition' => 'required|string|in:สภาพดี,สามารถซ่อมได้,ไม่สามารถซ่อมได้,พัง,อุปกรณ์ไม่พร้อมใช้งาน',
            'status' => 'required|string|in:available,unavailable,maintenance,retired',
            'notes' => 'nullable|string|max:500',
            'serial_number' => 'nullable|string|max:255',
        ];

        $data = $request->validate($rules);

        // Normalize serial number
        if (isset($data['serial_number'])) {
            $data['serial_number'] = trim($data['serial_number']);
        }

        // Temporarily disable uniqueness check to test basic validation
        \Log::info('Update attempt', [
            'equipment_item_id' => $equipmentItem->id,
            'data' => $data
        ]);

        $equipmentItem->update($data);

        Cache::forget('equipment_items_with_relations');

        return response()->json([
            'success' => true,
            'message' => 'Equipment item updated successfully',
            'data' => $equipmentItem->load(['equipment.category', 'accessories']),
        ]);
    }

    /**
     * Remove the specified equipment item
     */
    public function destroy(EquipmentItem $equipmentItem)
    {
        try {
            $equipmentItem->delete();

            Cache::forget('equipment_items_with_relations');

            return response()->json([
                'success' => true,
                'message' => 'Equipment item deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete equipment item: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get equipment items by equipment ID
     */
    public function getByEquipment($equipmentId)
    {
        $equipmentItems = EquipmentItem::with(['equipment.category', 'accessories'])
            ->where('equipment_id', $equipmentId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $equipmentItems
        ]);
    }

    /**
     * Bulk update equipment items
     */
    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'item_ids'   => 'required|array|min:1',
            'item_ids.*' => 'integer|exists:equipment_items,id',
            'status'     => 'required|string|in:available,unavailable,maintenance,retired',
            'condition'  => 'nullable|string|in:สภาพดี,สามารถซ่อมได้,ไม่สามารถซ่อมได้,พัง,อุปกรณ์ไม่พร้อมใช้งาน',
        ]);

        $updatedCount = EquipmentItem::whereIn('id', $data['item_ids'])
            ->update([
                'status'    => $data['status'],
                'condition' => $data['condition'] ?? null,
            ]);

        Cache::forget('equipment_items_with_relations');

        return response()->json([
            'success' => true,
            'message' => "Updated {$updatedCount} equipment items successfully"
        ]);
    }
}