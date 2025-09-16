<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('equipments')->get();
        return view('admin.category.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'status' => true,
            'category' => $category,
            'message' => 'Category created successfully'
        ]);
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($id);
        $category->update($validated);

        return response()->json([
            'status' => true,
            'category' => $category,
            'message' => 'Category updated successfully'
        ]);
    }


    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(
            [
                "status" => true,
                "message" => "Category deleted successfully"
            ]
        );
    }
}
