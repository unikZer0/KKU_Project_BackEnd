<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $equipments = Equipment::paginate(12);
    $categories = Category::all();
    return view('home', [
    'equipments' => $equipments,
    'categories' => $categories,
]);
    }
}
