<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
<<<<<<< HEAD
=======
use App\Models\Category;
>>>>>>> 1b243ed1a4868223f8efa956e9c82bd350d8f079

class HomeController extends Controller
{
    public function index()
    {
        $equipments = Equipment::all();
<<<<<<< HEAD
        return view('home', compact('equipments'));
=======
    $categories = Category::all();
    return view('home', compact('equipments', 'categories'));
>>>>>>> 1b243ed1a4868223f8efa956e9c82bd350d8f079
    }
}
