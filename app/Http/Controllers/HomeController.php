<?php

namespace App\Http\Controllers;

use App\Models\Equipments;

class HomeController extends Controller
{
    public function index()
    {
        $equipments = Equipments::all();
        return view('home', compact('equipments'));
    }
}

