<?php

namespace App\Http\Controllers\Borrowers;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;

use App\Models\Equipment;
use App\Models\Category;

class BorrowersHomeController extends Controller
{
    public function home()
    {
        $equipments = Equipment::all();
        $categories = Category::all();
        return view('home', compact('equipments', 'categories'));
    }
}
