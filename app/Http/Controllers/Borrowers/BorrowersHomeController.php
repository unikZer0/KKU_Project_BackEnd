<?php

namespace App\Http\Controllers\Borrowers;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;

use App\Models\Equipment;
use App\Models\Category;

class BorrowersHomeController extends Controller
{
//   public function index(Request $request)
// {
//     $query = Equipment::query();

//     if ($request->status) {
//         $query->where('status', $request->status);
//     }

//     if ($request->q) {
//         $query->where('name', 'like', "%{$request->q}%");
//     }

//     $equipments = $query->paginate(10);

//     return response()->json($equipments);
// }
  
}
