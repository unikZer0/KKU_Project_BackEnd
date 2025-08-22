<?php

namespace App\Http\Controllers;

use App\Models\Equipment;

class HomeController extends Controller
{
    public function index()
    {
        $equipments = Equipment::all();
        return view('home', compact('equipments'));
    }
}
