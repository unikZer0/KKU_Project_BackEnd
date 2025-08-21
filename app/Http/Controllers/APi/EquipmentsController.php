<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipments;

class EquipmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Equipments::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate(
            [
                "code" => "required|string|max:255",
                "name" => "required|string|max:255",
                "categories_id" => "required|integer|exists:categories,id",
                "status" => "required|in:available,unavailable,maintenance",
                "photo_path" => "nullable|string|max:255",
            ]
        );
        Equipments::create($data);

        return response()->json(
            [
                "status" => true,
                "message" => "Equipment created successfully",
                "data" => $data
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipments $equipment)
    {
        return response()->json(
            [
                "status" => true,
                "message" => "showing equipment",
                "data" => $equipment,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipments $equipment)
    {
        $request->validate([
            "name" => "sometimes|string|max:255",
            "code" => "sometimes|string|max:255",
            "categories_id" => "sometimes|integer|exists:categories,id",
            "status" => "sometimes|in:available,unavailable,maintenance",
            "photo_path" => "sometimes|string|max:255",
        ]);

        $equipment->update($request->all());

        return response()->json(
            [
                "status" => true,
                "message" => "Equipment updated successfully",
                "data" => $equipment->fresh()
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipments $equipment)
    {
        $equipment->delete();

        return response()->json(
            [
                "status" => true,
                "message" => "equipment deleted succesfully"
            ]
        );
    }
}
