<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{


    public function index(Request $request)
    {
        $query = Equipment::query();

        if ($request->filled('category')) {
            $categoryCode = (string) $request->get('category');
            $categoryId = Category::where('cate_id', $categoryCode)->value('id');
            if ($categoryId) {
                $query->where('categories_id', $categoryId);
            }
        }

        if ($request->filled('status')) {
            $status = (string) $request->get('status');
            $allowed = ['available', 'retired', 'maintenance'];
            if (in_array($status, $allowed, true)) {
                $query->where('status', $status);
            }
        }

        $page = $request->get('page', 1);
        $queryString = $request->getQueryString();
        $equipmentsCacheKey = "equipments:page:{$page}:{$queryString}";

        $equipments = Cache::remember($equipmentsCacheKey, now()->addMinutes(5), function () use ($query) {
            return $query->paginate(15)->withQueryString();
        });

        $categories = Cache::remember('categories:all', now()->addMinutes(10), function () {
            return Category::all();
        });

        return view('home', [
            'equipments' => $equipments,
            'categories' => $categories,
        ]);
    }
}
