<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $equipments = Equipment::all();
    $categories = Category::all();
    return view('home', compact('equipments', 'categories'));
    }
}
