<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::query();

        // Filter by category code if provided (?category=CODE)
        if ($request->filled('category')) {
            $categoryCode = (string) $request->get('category');
            $categoryId = Category::where('cate_id', $categoryCode)->value('id');
            if ($categoryId) {
                $query->where('categories_id', $categoryId);
            }
        }

        // Filter by equipment status if provided (?status=available|unavailable|maintenance)
        if ($request->filled('status')) {
            $status = (string) $request->get('status');
            $allowed = ['available', 'unavailable', 'maintenance'];
            if (in_array($status, $allowed, true)) {
                $query->where('status', $status);
            }
        }

        $equipments = $query->paginate(12)->withQueryString();

        $categories = Category::all();
        return view('home', [
            'equipments' => $equipments,
            'categories' => $categories,
        ]);
    }
}
