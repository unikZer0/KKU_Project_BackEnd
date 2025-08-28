<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;

class CategoryController extends Controller
{
    //!DISPLAY ALL CATEGORIES
    public function index()
    {
        $categories = Category::withCount('equipments')->get();
        return view('admin.category.index', compact('categories'));
    }

    //!DISPLAY CREATE FORM
    public function create()
    {
        $categories = Category::all();
        return view('admin.category.create', compact('categories'));
    }

    //!STORE A NEW CATEGORY
    public function store(Request $request)
    {
        $category = new Category();
        $category->name = $request->input('name');
        $category->save();

        return redirect()->route('admin.category.index')->with('success', 'Category added successfully.');
    }

    //!DISPLAY EDIT FORM
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.category.edit', compact('category'));
    }

    //!UPDATE CATEGORY INFO
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);
        $category->name = $request->input('name');
        $category->save();

        return redirect()->route('admin.category.index')->with('success', 'Category updated successfully.');
    }

    //!DELETE CATEGORY
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.category.index')->with('success', 'Category deleted successfully.');
    }
}
