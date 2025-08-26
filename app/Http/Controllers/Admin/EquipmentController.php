<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CloudinaryService;
use App\Models\Category;
use App\Models\Equipment;

class EquipmentController extends Controller
{
    //!VIEW
    public function index()
    {
        $equipments = Equipment::with('category')->get();
        return view('admin.equipment.index', compact('equipments'));
    }


    //!ADD FORM
    public function create()
    {
        $categories = Category::all();
        return view('admin.equipment.create', compact('categories'));
    }

    //! TEST UPLOAD FORM
    public function test_upload_form()
    {
        $categories = Category::all();
        return view('admin.equipment.test-upload', compact('categories'));
    }

    //! CREATE EQUIPMENTS
    public function store(Request $request, CloudinaryService $cloudinary)
    {
        $data = $request->validate([
            "name" => "required|string|max:20",
            "description" => "required|string|max:255",
            "categories_id" => "required|integer|exists:categories,id",
            "status" => "required|in:available,unavailable,maintenance",
            "photo_path" => "nullable|string|max:255",
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image')->getRealPath();
            $url = $cloudinary->upload($file, 'equipment');
            $data['photo_path'] = $url;
        }

        $equipment = Equipment::create($data);

        return response()->json([
            "status" => true,
            "message" => "Equipment created successfully",
            "data" => $equipment
        ]);
    }

    //!EDIT FORM
    public function edit($id)
    {
        $equipment = Equipment::findOrFail($id);
        $categories = Category::all();
        return view('admin.equipment.edit', compact('equipment', 'categories'));
    }

    public function update(Request $request, $id, CloudinaryService $cloudinary)
    {
        $equipment = Equipment::findOrFail($id);
        $data = $request->validate([
            "name" => "required|string|max:20",
            "description" => "required|string|max:255",
            "categories_id" => "required|integer|exists:categories,id",
            "status" => "required|in:available,unavailable,maintenance",
            "photo_path" => "nullable|string|max:255",
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image')->getRealPath();
            $url = $cloudinary->upload($file, 'equipment');
            $data['photo_path'] = $url;
        }

        $equipment->update($data);

        return response()->json([
            "status" => true,
            "message" => "Equipment updated successfully",
            "data" => $equipment
        ]);
    }

    //!DELETE
    public function delete($id)
    {
        $equipment = Equipment::findOrFail($id);
        $equipment->delete();

        return response()->json(
            [
                "status" => true,
                "message" => "Equipment deleted successfully"
            ]
        );
    }
}
