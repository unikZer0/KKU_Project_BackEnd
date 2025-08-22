<?php

namespace App\Http\Controllers\Borrowers;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;

use App\Models\Equipments;

class BorrowersHomeController extends Controller
{
    public function home()
    {
        $equipments = Equipments::all();
        $categories = Categories::all();
        return view('home', compact('equipments', 'categories'));
    }
}
