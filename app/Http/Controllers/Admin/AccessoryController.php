<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EquipmentAccessory;
use App\Models\Equipment;
use App\Models\EquipmentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessoryController extends Controller
{
    public function index(Request $request)
    {
        $query = EquipmentAccessory::with(['equipment', 'equipmentItem']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('equipment', function ($equipmentQuery) use ($search) {
                      $equipmentQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by condition
        if ($request->filled('condition') && $request->condition !== 'all') {
            $query->where('condition', $request->condition);
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by equipment
        if ($request->filled('equipment') && $request->equipment !== 'all') {
            $query->where('equipment_id', $request->equipment);
        }

        $accessories = $query->orderBy('created_at', 'desc')->get();
        $equipments = Equipment::with('category')->get();

        return view('admin.accessories.index', compact('accessories', 'equipments'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'equipment_item_id' => 'nullable|exists:equipment_items,id',
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:equipment_accessories,serial_number',
            'condition' => 'required|in:good,fair,poor',
            'status' => 'required|in:available,unavailable,maintenance',
            'description' => 'nullable|string'
        ]);

        try {
            $accessory = EquipmentAccessory::create($validated);
            $accessory->load(['equipment', 'equipmentItem']);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'อุปกรณ์เสริมถูกสร้างเรียบร้อยแล้ว',
                    'accessory' => $accessory
                ]);
            }
            
            return redirect()->route('admin.accessories.index')
                ->with('success', 'อุปกรณ์เสริมถูกสร้างเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการสร้างอุปกรณ์เสริม: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการสร้างอุปกรณ์เสริม: ' . $e->getMessage());
        }
    }


    public function update(Request $request, EquipmentAccessory $accessory)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'equipment_item_id' => 'nullable|exists:equipment_items,id',
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:equipment_accessories,serial_number,' . $accessory->id,
            'condition' => 'required|in:good,fair,poor',
            'status' => 'required|in:available,unavailable,maintenance',
            'description' => 'nullable|string'
        ]);

        try {
            $accessory->update($validated);
            $accessory->load(['equipment', 'equipmentItem']);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'อุปกรณ์เสริมถูกอัปเดตเรียบร้อยแล้ว',
                    'accessory' => $accessory
                ]);
            }
            
            return redirect()->route('admin.accessories.index')
                ->with('success', 'อุปกรณ์เสริมถูกอัปเดตเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการอัปเดตอุปกรณ์เสริม: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการอัปเดตอุปกรณ์เสริม: ' . $e->getMessage());
        }
    }

    public function destroy(EquipmentAccessory $accessory)
    {
        try {
            $accessory->delete();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'อุปกรณ์เสริมถูกลบเรียบร้อยแล้ว'
                ]);
            }
            
            return redirect()->route('admin.accessories.index')
                ->with('success', 'อุปกรณ์เสริมถูกลบเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการลบอุปกรณ์เสริม: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->with('error', 'เกิดข้อผิดพลาดในการลบอุปกรณ์เสริม: ' . $e->getMessage());
        }
    }

    public function getByEquipment(Request $request)
    {
        $equipmentId = $request->equipment_id;
        $items = EquipmentItem::where('equipment_id', $equipmentId)->get();
        return response()->json($items);
    }
}
