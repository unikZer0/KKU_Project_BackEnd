<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EquipmentController extends Controller
{
    use LogsActivity;
    //? INDEX
    public function index()
    {
        $equipments = Cache::remember('equipments_with_category', 600, function () {
            return Equipment::with(['category', 'items', 'accessories', 'specifications'])->get();
        });

        $categories = Cache::remember('all_categories', 600, function () {
            return Category::all();
        });

        return view('admin.equipment.index', [
            'equipments' => $equipments,
            'categories' => Category::all(),
        ]);
    }
    //? STORE
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                "code" => "nullable|string|unique:equipments,code|max:10",
                "name" => "required|string",
                "description" => "nullable|string",
                "category_id" => "required|integer|exists:categories,id",
                "brand" => "nullable|string|max:255",
                "model" => "nullable|string|max:255",
                "items" => "required|array|min:1",
                "items.*.serial_number" => "nullable|string|max:255",
                "items.*.condition" => "required|string|in:สภาพดี,สามารถซ่อมได้,ไม่สามารถซ่อมได้,พัง,อุปกรณ์ไม่พร้อมใช้งาน",
                "items.*.status" => "required|string|in:available,unavailable,maintenance",
                "accessories" => "nullable|array",
                "accessories.*.name" => "required|string|max:255",
                "accessories.*.description" => "nullable|string",
                "accessories.*.serial_number" => "nullable|string|max:255",
                "accessories.*.equipment_item_id" => "nullable|integer",
                "accessories.*.condition" => "required|string|in:สภาพดี,สามารถซ่อมได้,ไม่สามารถซ่อมได้,พัง,อุปกรณ์ไม่พร้อมใช้งาน",
                "accessories.*.status" => "required|string|in:available,unavailable",
                "specifications" => "nullable|array",
                "specifications.*.spec_key" => "required|string|max:100",
                "specifications.*.spec_value_text" => "nullable|string|max:255",
                "specifications.*.spec_value_number" => "nullable|numeric",
                "specifications.*.spec_value_bool" => "nullable|boolean",
                "images.*" => "image|mimes:jpg,jpeg,png,webp,gif|max:5120",
            ]);

            $paths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('equipments', 'public');
                    $paths[] = '/storage/' . $path;
                }
            }

            $data['photo_path'] = json_encode($paths);
            
            // Create equipment
            $equipment = Equipment::create($data);

            // Create equipment items
            foreach ($data['items'] as $itemData) {
                $equipment->items()->create([
                    'serial_number' => $itemData['serial_number'] ?: $data['code'] . '-' . str_pad(($equipment->items()->count() + 1), 3, '0', STR_PAD_LEFT),
                    'condition' => $itemData['condition'],
                    'status' => $itemData['status']
                ]);
            }

            // Create accessories - add to specific equipment items
            if (!empty($data['accessories'])) {
                foreach ($data['accessories'] as $accessoryData) {
                    // Find the equipment item by index if equipment_item_id is provided
                    $targetItem = null;
                    if (isset($accessoryData['equipment_item_id']) && $accessoryData['equipment_item_id'] !== null) {
                        $itemIndex = $accessoryData['equipment_item_id'];
                        if (isset($equipment->items[$itemIndex])) {
                            $targetItem = $equipment->items[$itemIndex];
                        }
                    }
                    
                    if ($targetItem) {
                        \App\Models\EquipmentAccessory::create([
                            'equipment_id' => $equipment->id,
                            'equipment_item_id' => $targetItem->id,
                            'name' => $accessoryData['name'],
                            'description' => $accessoryData['description'] ?? '',
                            'serial_number' => $accessoryData['serial_number'],
                            'condition' => $accessoryData['condition'],
                            'status' => $accessoryData['status']
                        ]);
                    }
                }
            }

            // Create specifications
            if (!empty($data['specifications'])) {
                foreach ($data['specifications'] as $specData) {
                    \App\Models\EquipmentSpecification::create([
                        'equipment_id' => $equipment->id,
                        'spec_key' => $specData['spec_key'],
                        'spec_value_text' => $specData['spec_value_text'],
                        'spec_value_number' => $specData['spec_value_number'],
                        'spec_value_bool' => $specData['spec_value_bool']
                    ]);
                }
            }

            $this->logEquipment('create', $equipment, [
                'description' => "เพิ่มอุปกรณ์ใหม่ '{$equipment->name}' พร้อมรายการอุปกรณ์ " . count($data['items']) . " รายการ",
                'severity' => 'info'
            ]);

            Cache::forget('equipments_with_category');

            return response()->json([
                "status" => true,
                "message" => "Equipment created successfully with " . count($data['items']) . " items",
                "data" => $equipment->load(['category', 'items', 'accessories', 'specifications'])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $errorMessages = [];
            foreach ($errors as $field => $messages) {
                $errorMessages = array_merge($errorMessages, $messages);
            }
            return response()->json([
                "status" => false,
                "message" => implode(', ', $errorMessages),
                "errors" => $errors
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Server error: " . $e->getMessage()
            ], 500);
        }
    }
    //? UPDATE
    public function update(Request $request, $id)
    {
        try {
            $equipment = Equipment::findOrFail($id);

            $validatedData = $request->validate([
                "code" => "nullable|string|max:10",
                "name" => "required|string",
                "description" => "nullable|string",
                "category_id" => "required|integer|exists:categories,id",
                "brand" => "nullable|string|max:255",
                "model" => "nullable|string|max:255",
                "items" => "nullable|array",
                "items.*.id" => "nullable|integer|exists:equipment_items,id",
                "items.*.serial_number" => "nullable|string|max:255",
                "items.*.condition" => "required|string|in:Good,Fair,Poor",
                "items.*.status" => "required|string|in:available,unavailable,maintenance",
                "accessories" => "nullable|array",
                "accessories.*.id" => "nullable|integer|exists:equipment_accessories,id",
                "accessories.*.name" => "required|string|max:255",
                "accessories.*.description" => "nullable|string",
                "accessories.*.serial_number" => "nullable|string|max:255",
                "accessories.*.equipment_item_id" => "nullable|integer|exists:equipment_items,id",
                "accessories.*.condition" => "required|string|in:Good,Fair,Poor",
                "accessories.*.status" => "required|string|in:available,unavailable",
                "specifications" => "nullable|array",
                "specifications.*.id" => "nullable|integer|exists:equipment_specifications,id",
                "specifications.*.spec_key" => "required|string|max:100",
                "specifications.*.spec_value_text" => "nullable|string|max:255",
                "specifications.*.spec_value_number" => "nullable|numeric",
                "specifications.*.spec_value_bool" => "nullable|boolean",
                "images.*" => "image|mimes:jpg,jpeg,png,webp,gif|max:5120",
                "images_to_delete" => "nullable|array",
                "images_to_delete.*" => "string",
                "selected_main_identifier" => "nullable|string",
                "deleted_items" => "nullable|array",
                "deleted_items.*" => "integer|exists:equipment_items,id",
            ]);

            // Update basic equipment data
            $equipment->update([
                'code' => $validatedData['code'],
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'category_id' => $validatedData['category_id'],
                'brand' => $validatedData['brand'],
                'model' => $validatedData['model'],
            ]);

            // Delete items that were marked for deletion
            if (isset($validatedData['deleted_items']) && !empty($validatedData['deleted_items'])) {
                foreach ($validatedData['deleted_items'] as $itemId) {
                    $item = \App\Models\EquipmentItem::find($itemId);
                    if ($item && $item->equipment_id === $equipment->id) {
                        // Check if this item has any borrow requests through borrow_request_items
                        $hasBorrowRequests = \App\Models\BorrowRequestItem::where('equipment_item_id', $itemId)->exists();
                        if (!$hasBorrowRequests) {
                            // Delete accessories for this item
                            $item->accessories()->delete();
                            // Delete the item
                            $item->delete();
                        }
                    }
                }
            }

            // Update equipment items
            if (isset($validatedData['items'])) {
                foreach ($validatedData['items'] as $itemData) {
                    if (isset($itemData['id'])) {
                        // Update existing item
                        $item = \App\Models\EquipmentItem::find($itemData['id']);
                        if ($item) {
                            $item->update([
                                'serial_number' => $itemData['serial_number'],
                                'condition' => $itemData['condition'],
                                'status' => $itemData['status']
                            ]);
                        }
                    } else {
                        // Create new item
                        $equipment->items()->create([
                            'serial_number' => $itemData['serial_number'] ?: $equipment->code . '-' . str_pad(($equipment->items()->count() + 1), 3, '0', STR_PAD_LEFT),
                            'condition' => $itemData['condition'],
                            'status' => $itemData['status']
                        ]);
                    }
                }
            }

            // Update accessories
            if (isset($validatedData['accessories'])) {
                foreach ($validatedData['accessories'] as $accessoryData) {
                    if (isset($accessoryData['id'])) {
                        // Update existing accessory
                        $accessory = \App\Models\EquipmentAccessory::find($accessoryData['id']);
                        if ($accessory) {
                            $accessory->update([
                                'name' => $accessoryData['name'],
                                'description' => $accessoryData['description'] ?? '',
                                'serial_number' => $accessoryData['serial_number'],
                                'equipment_item_id' => $accessoryData['equipment_item_id'] ?? null,
                                'condition' => $accessoryData['condition'],
                                'status' => $accessoryData['status']
                            ]);
                        }
                    } else {
                        // Create new accessory
                        \App\Models\EquipmentAccessory::create([
                            'equipment_id' => $equipment->id,
                            'equipment_item_id' => $accessoryData['equipment_item_id'] ?? null,
                            'name' => $accessoryData['name'],
                            'description' => $accessoryData['description'] ?? '',
                            'serial_number' => $accessoryData['serial_number'],
                            'condition' => $accessoryData['condition'],
                            'status' => $accessoryData['status']
                        ]);
                    }
                }
            }

            // Update specifications
            if (isset($validatedData['specifications'])) {
                foreach ($validatedData['specifications'] as $specData) {
                    if (isset($specData['id'])) {
                        // Update existing specification
                        $spec = \App\Models\EquipmentSpecification::find($specData['id']);
                        if ($spec) {
                            $spec->update([
                                'spec_key' => $specData['spec_key'],
                                'spec_value_text' => $specData['spec_value_text'],
                                'spec_value_number' => $specData['spec_value_number'],
                                'spec_value_bool' => $specData['spec_value_bool']
                            ]);
                        }
                    } else {
                        // Create new specification
                        \App\Models\EquipmentSpecification::create([
                            'equipment_id' => $equipment->id,
                            'spec_key' => $specData['spec_key'],
                            'spec_value_text' => $specData['spec_value_text'],
                            'spec_value_number' => $specData['spec_value_number'],
                            'spec_value_bool' => $specData['spec_value_bool']
                        ]);
                    }
                }
            }

            // Handle images
            $currentPhotos = json_decode($equipment->photo_path, true) ?? [];

            if ($request->filled('images_to_delete')) {
                $imagesToDelete = $request->input('images_to_delete');
                $currentPhotos = array_values(array_diff($currentPhotos, $imagesToDelete));
                foreach ($imagesToDelete as $imageUrl) {
                    $path = str_replace('/storage/', '', $imageUrl);
                    Storage::disk('public')->delete($path);
                }
            }

            $newlyUploadedPhotos = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('equipments', 'public');
                    $newlyUploadedPhotos[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'path' => '/storage/' . $path,
                    ];
                }
            }
            $newPhotoPaths = array_column($newlyUploadedPhotos, 'path');
            $finalPhotoList = array_merge(
                is_array($currentPhotos) ? $currentPhotos : [],
                is_array($newPhotoPaths) ? $newPhotoPaths : []
            );

            $mainPhotoIdentifier = $request->input('selected_main_identifier');
            $mainPhotoUrl = null;
            if ($mainPhotoIdentifier) {
                if (in_array($mainPhotoIdentifier, $finalPhotoList)) {
                    $mainPhotoUrl = $mainPhotoIdentifier;
                } else {
                    foreach ($newlyUploadedPhotos as $uploadedPhoto) {
                        if ($uploadedPhoto['original_name'] === $mainPhotoIdentifier) {
                            $mainPhotoUrl = $uploadedPhoto['path'];
                            break;
                        }
                    }
                }
            }
            if ($mainPhotoUrl) {
                $finalPhotoList = array_values(array_diff($finalPhotoList, [$mainPhotoUrl]));
                array_unshift($finalPhotoList, $mainPhotoUrl);
            }

            $equipment->update(['photo_path' => json_encode(array_values($finalPhotoList))]);

            // Log equipment update
            $this->logEquipment('update', $equipment, [
                'description' => "แก้ไขข้อมูลอุปกรณ์ '{$equipment->name}' เรียบร้อย",
                'severity' => 'info'
            ]);

            Cache::forget('equipments_with_category');

            return response()->json([
                "status" => true,
                "message" => "Equipment updated successfully",
                "data" => $equipment->fresh()->load(['category', 'items', 'accessories', 'specifications'])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "status" => false,
                "message" => "Validation error",
                "errors" => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Server error: " . $e->getMessage()
            ], 500);
        }
    }
    //? DELETE
    public function destroy($id)
    {
        try {
            $equipment = Equipment::findOrFail($id);

            // Check if equipment has any borrow requests
            $hasBorrowRequests = \App\Models\BorrowRequest::where('equipment_id', $id)->exists();
            if ($hasBorrowRequests) {
                return response()->json([
                    "status" => false,
                    "message" => "ไม่สามารถลบอุปกรณ์ที่มีคำขอยืมได้ กรุณายกเลิกหรือดำเนินการคำขอที่เกี่ยวข้องให้เสร็จสิ้นก่อน"
                ], 400);
            }

            // Delete related records first to avoid foreign key constraints
            // Delete accessories
            $equipment->accessories()->delete();
            
            // Delete equipment items
            $equipment->items()->delete();
            
            // Delete specifications
            $equipment->specifications()->delete();

            // Delete photos from storage
            if ($equipment->photo_path) {
                $oldPhotos = json_decode($equipment->photo_path, true) ?? [];
                foreach ($oldPhotos as $photo) {
                    $path = str_replace('/storage/', '', $photo);
                    Storage::disk('public')->delete($path);
                }
            }

            // Log equipment deletion
            $this->logEquipment('delete', $equipment, [
                'description' => "ลบอุปกรณ์ '{$equipment->name}' ออกจากระบบเรียบร้อย",
                'severity' => 'warning'
            ]);

            // Delete the equipment
            $equipment->delete();

            // Clear cache
            Cache::forget('equipments_with_category');

            return response()->json([
                "status" => true,
                "message" => "ลบอุปกรณ์เรียบร้อยแล้ว"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "เกิดข้อผิดพลาดในการลบอุปกรณ์: " . $e->getMessage()
            ], 500);
        }
    }
    
    //? DELETE EQUIPMENT ITEM
    public function destroyItem($equipmentId, $itemId)
    {
        try {
            $equipment = Equipment::findOrFail($equipmentId);
            $item = $equipment->items()->findOrFail($itemId);

            // Check if this item has any borrow requests through borrow_request_items
            $hasBorrowRequests = \App\Models\BorrowRequestItem::where('equipment_item_id', $itemId)->exists();
            if ($hasBorrowRequests) {
                return response()->json([
                    "status" => false,
                    "message" => "ไม่สามารถลบรายการอุปกรณ์ที่มีคำขอยืมได้ กรุณายกเลิกหรือดำเนินการคำขอที่เกี่ยวข้องให้เสร็จสิ้นก่อน"
                ], 400);
            }

            // Delete accessories for this item
            $item->accessories()->delete();
            
            // Delete the item
            $item->delete();

            // Check if this was the last item - if so, delete the entire equipment
            $remainingItems = $equipment->items()->count();
            if ($remainingItems === 0) {
                // Delete the entire equipment if no items remain
                $equipment->specifications()->delete();
                
                // Delete photos from storage
                if ($equipment->photo_path) {
                    $oldPhotos = json_decode($equipment->photo_path, true) ?? [];
                    foreach ($oldPhotos as $photo) {
                        $path = str_replace('/storage/', '', $photo);
                        \Storage::disk('public')->delete($path);
                    }
                }
                
                $equipment->delete();
                
                $this->logEquipment('delete_equipment', $equipment, [
                    'description' => "ลบอุปกรณ์ '{$equipment->name}' ออกจากระบบ (ไม่มีรายการอุปกรณ์เหลือ)",
                    'severity' => 'warning'
                ]);
                
                Cache::forget('equipments_with_category');
                
                return response()->json([
                    "status" => true,
                    "message" => "รายการอุปกรณ์ถูกลบเรียบร้อย (เนื่องจากไม่มีรายการเหลือ)",
                    "deleted_equipment" => true
                ]);
            }

            $this->logEquipment('delete_item', $equipment, [
                'description' => "ลบรายการอุปกรณ์จาก '{$equipment->name}' เรียบร้อย",
                'severity' => 'info'
            ]);

            Cache::forget('equipments_with_category');

            return response()->json([
                "status" => true,
                "message" => "รายการอุปกรณ์ถูกลบเรียบร้อย",
                "data" => $equipment->fresh()->load(['category', 'items', 'accessories', 'specifications'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Server error: " . $e->getMessage()
            ], 500);
        }
    }
}
