<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CloudinaryService;
use App\Models\Log;
use App\Models\Category;
use App\Models\Equipment;

class EquipmentController extends Controller
{
    //!VIEW EQUIPMENTS
    public function index()
    {
        $equipments = Equipment::with('category')->get();
        $categories = Category::all();
        return view('admin.equipment.index', compact('equipments', 'categories'));
    }

    //!CREATE EQUIPMENTS FORM
    public function create()
    {
        $categories = Category::all();
        return view('admin.equipment.create', compact('categories'));
    }

    //! TEST UPLOAD FORM - UnikZer0
    public function test_upload_form()
    {
        $categories = Category::all();
        return view('admin.equipment.test-upload', compact('categories'));
    }

    //! STORE AN EQUIPMENT INTO DATABASE
    public function store(Request $request, CloudinaryService $cloudinary)
    {
        $data = $request->validate([
            "name" => "required|string|max:20",
            "description" => "required|string|max:255",
            "categories_id" => "required|integer|exists:categories,id",
            "status" => "required|in:available,retired,maintenance",
            "photo_path" => "string|max:255",
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image')->getRealPath();
            $url = $cloudinary->upload($file, 'equipment');
            $data['photo_path'] = $url;
        }

        $equipment = Equipment::create($data);

Log::create([
    'admin_id' => auth()->id(),
    'action' => 'create',
    'target_type' => 'equipment',
    'target_id' => $equipment->id,
    'description' => "Created equipment: {$equipment->name} (ID {$equipment->id})",
]);
        if ($request->wantsJson()) {
            return response()->json([
                "status" => true,
                "message" => "Equipment created successfully",
                "data" => $equipment,
            ]);
        }

        return redirect()->route('admin.equipment.index')
            ->with('success', 'เพิ่มอุปกรณ์เรียบร้อยแล้ว');
    }

    //!EDIT FORM
    public function edit($id)
    {
        $equipment = Equipment::findOrFail($id);
        $categories = Category::all();
        return view('admin.equipment.edit', compact('equipment', 'categories'));
    }

    //!UPDATE EQUIPMENTS INFO
public function update(Request $request, $id, CloudinaryService $cloudinary)
{
    $equipment = Equipment::findOrFail($id);

    // Save the old name for logging
    $oldName = $equipment->name;

    // Validate incoming data
    $data = $request->validate([
        "name" => "required|string|max:20",
        "description" => "nullable|string|max:255",
        "categories_id" => "required|integer|exists:categories,id",
        "status" => "required|in:available,retired,maintenance",
        "photo_path" => "nullable|string|max:255",
    ]);

    // Handle image upload
    if ($request->hasFile('image')) {
        $file = $request->file('image')->getRealPath();
        $url = $cloudinary->upload($file, 'equipment');
        $data['photo_path'] = $url;
    }

    // Update the equipment
    $equipment->update($data);

    // Log the update
    Log::create([
        'admin_id' => auth()->id(),
        'action' => 'update',
        'target_type' => 'equipment',
        'target_id' => $equipment->id,
        'description' => "Updated equipment: {$oldName} → {$equipment->name} (ID {$equipment->id})",
    ]);

    return response()->json([
        "status" => true,
        "message" => "Equipment updated successfully",
        "data" => $equipment->load('category')
    ]);
}
    //!DELETE EQUIPMENTS
    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);
    Log::create([
        'admin_id' => auth()->id(),
        'action' => 'delete',
        'target_type' => 'equipment',
        'target_id' => $id,
        'description' => "Deleted equipment: {$equipment->name} (ID {$equipment->code})",
    ]);
        $equipment->delete();

        return response()->json(
            [
                "status" => true,
                "message" => "Equipment deleted successfully"
            ]
        );
    }
}
