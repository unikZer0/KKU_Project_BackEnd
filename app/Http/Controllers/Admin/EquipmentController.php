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
        return view('admin.equipment');
    }

    //!ADD
    public function add_equipment()
    {
        $categories = Category::all();
        return view('admin.equipment.add', compact('categories'));
    }

    //! TEST UPLOAD FORM
    public function test_upload_form()
    {
        $categories = Category::all();
        return view('admin.equipment.test-upload', compact('categories'));
    }

    //! UPLOAD
public function upload_product(Request $request, CloudinaryService $cloudinary)
{
    $data = $request->validate([
        "code" => "required|string|max:255",
        "name" => "required|string|max:255",
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

    //!EDIT
    public function edit_equipment($id)
    {
        $equipment = Equipment::findOrFail($id);
        $categories = Category::all();
        return view('admin.equipment.edit', compact('equipment', 'categories'));
    }

    //!DELETE
    public function delete_equipment($id)
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
