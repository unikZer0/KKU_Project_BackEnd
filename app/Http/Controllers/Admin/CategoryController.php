<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    use LogsActivity;
    //index
    public function index()
    {
        $categories = Category::withCount('equipments')->get();
        return view('admin.category.index', compact('categories'));
    }

    //store logic
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create($validated);

        // Log category creation
        $this->logCategory('create', $category, [
            'description' => "เพิ่มหมวดหมู่ใหม่ '{$category->name}' เข้าสู่ระบบ",
            'severity' => 'info'
        ]);

        return response()->json([
            'status' => true,
            'category' => $category,
            'message' => 'Category created successfully'
        ]);
    }

    //Update logic
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($id);
        $category->update($validated);

        // Log category update
        $this->logCategory('update', $category, [
            'description' => "แก้ไขข้อมูลหมวดหมู่ '{$category->name}' เรียบร้อย",
            'severity' => 'info'
        ]);

        return response()->json([
            'status' => true,
            'category' => $category,
            'message' => 'Category updated successfully'
        ]);
    }

    //delete logic
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        
        // Log category deletion
        $this->logCategory('delete', $category, [
            'description' => "ลบหมวดหมู่ '{$category->name}' ออกจากระบบเรียบร้อย",
            'severity' => 'warning'
        ]);
        
        $category->delete();

        return response()->json(
            [
                "status" => true,
                "message" => "Category deleted successfully"
            ]
        );
    }
}
