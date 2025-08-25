<?php

namespace App\Http\Controllers;

use App\Models\Equipment;

class PublicEquipmentController extends Controller
{
public function show(Equipment $equipment)
{
    return view('equipments.show', compact('equipment'));
}


}
