<?php

namespace App\Http\Controllers;

use App\Models\Equipment;

class PublicEquipmentController extends Controller
{
    public function show($id)
{
    $equipment = Equipment::findOrFail($id);
    return view('equipments.show', compact('equipment'));
}

}
