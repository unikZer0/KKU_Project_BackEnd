<?php

namespace App\Http\Controllers\Admin;

use App\Models\Equipment;
use App\Models\EquipmentSpecification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EquipmentSpecificationController extends Controller
{
    public function store(Request $request, $equipmentId)
    {
        $equipment = Equipment::findOrFail($equipmentId);

        $data = $request->validate([
            'spec_key' => 'required|string|max:100',
            'spec_value_text' => 'nullable|string|max:255',
            'spec_value_number' => 'nullable|numeric',
            'spec_value_bool' => 'nullable|boolean',
        ]);

        $equipment->specifications()->create($data);

        return back()->with('success', 'Specification added');
    }

    public function destroy($equipmentId, $specId)
    {
        EquipmentSpecification::where('equipment_id', $equipmentId)
            ->where('id', $specId)
            ->delete();

        return back()->with('success', 'Specification removed');
    }
}
