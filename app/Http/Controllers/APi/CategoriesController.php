<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Category::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate(
            [
                "id" => "required|integer",
                "name" => "required|string|max:255",
            ]
        );
        Category::create($data);

        return response()->json(
            [
                "status" => true,
                "message" => "Category created successfully",
                "data" => $data
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json(
            [
                "status" => true,
                "message" => "showing category",
                "data" => $category,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
