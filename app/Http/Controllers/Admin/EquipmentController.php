<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    //!VIEW EQUIPMENTS
    public function index()
    {
        $equipments = Equipment::with('category')->get();
        $categories = Category::all();
        return view('admin.equipment.index', compact('equipments', 'categories'));
    }

    //! STORE AN EQUIPMENT INTO DATABASE
    public function store(Request $request)
    {
        try {
            // Validate incoming data
            $data = $request->validate([
                "code" => "nullable|string|unique:equipments,code|max:10",
                "name" => "required|string|max:20",
                "description" => "nullable|string|max:255",
                "categories_id" => "required|integer|exists:categories,id",
                "status" => "required|in:available,retired,maintenance",
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('equipments', 'public');
                $data['photo_path'] = '/storage/' . $path; // For frontend
            }

            // Create equipment
            $equipment = Equipment::create($data);

            // Log creation
            Log::create([
                'admin_id' => auth()->id(),
                'action' => 'create',
                'target_type' => 'equipment',
                'target_id' => $equipment->id,
                'description' => "Created equipment: {$equipment->name} (ID {$equipment->id})",
            ]);

            return response()->json([
                "status" => true,
                "message" => "Equipment created successfully",
                "data" => $equipment->load('category')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "status" => false,
                "message" => "Validation failed",
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Server error: " . $e->getMessage()
            ], 500);
        }
    }

    //! UPDATE EQUIPMENTS INFO
    public function update(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);
        $oldName = $equipment->name;

        // Validate incoming data
        $data = $request->validate([
            "code" => "required|string|unique:equipments,code,{$id}|max:10",
            "name" => "required|string|max:20",
            "description" => "nullable|string|max:255",
            "categories_id" => "required|integer|exists:categories,id",
            "status" => "required|in:available,retired,maintenance",
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($equipment->photo_path) {
                $oldPath = str_replace('/storage/', '', $equipment->photo_path);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('equipments', 'public');
            $data['photo_path'] = '/storage/' . $path;
        }

        // Update equipment
        $equipment->update($data);

        // Log update
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

    //! DELETE EQUIPMENTS
    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);

        // Delete image from storage if exists
        if ($equipment->photo_path) {
            $oldPath = str_replace('/storage/', '', $equipment->photo_path);
            Storage::disk('public')->delete($oldPath);
        }

        // Log deletion
        Log::create([
            'admin_id' => auth()->id(),
            'action' => 'delete',
            'target_type' => 'equipment',
            'target_id' => $id,
            'description' => "Deleted equipment: {$equipment->name} (ID {$equipment->code})",
        ]);

        $equipment->delete();

        return response()->json([
            "status" => true,
            "message" => "Equipment deleted successfully"
        ]);
    }
}
