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

        // Category filter
        if ($request->filled('category')) {
            $categoryCode = (string) $request->get('category');
            $categoryId = Category::where('cate_id', $categoryCode)->value('id');
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }
        }

        // Brand filter
        if ($request->filled('brand')) {
            $brand = (string) $request->get('brand');
            $query->where('brand', $brand);
        }

        // Availability filter (based on equipment items status)
        if ($request->filled('availability')) {
            $availability = (string) $request->get('availability');
            if ($availability === 'available') {
                $query->whereHas('items', function ($q) {
                    $q->where('status', 'available');
                });
            } elseif ($availability === 'unavailable') {
                $query->whereDoesntHave('items', function ($q) {
                    $q->where('status', 'available');
                });
            }
        }


        // Specs filter (dynamic based on equipment specifications)
        if ($request->filled('specs')) {
            $specs = $request->get('specs');
            if (is_array($specs)) {
                foreach ($specs as $specKey => $specValue) {
                    if (!empty($specValue)) {
                        $query->whereHas('specifications', function ($q) use ($specKey, $specValue) {
                            $q->where('spec_key', $specKey);
                            if (is_numeric($specValue)) {
                                $q->where('spec_value_number', $specValue);
                            } else {
                                $q->where('spec_value_text', 'like', "%{$specValue}%");
                            }
                        });
                    }
                }
            }
        }

        $page = $request->get('page', 1);
        $queryString = $request->getQueryString();
        $equipmentsCacheKey = "equipments:page:{$page}:{$queryString}";

        $equipments = Cache::remember($equipmentsCacheKey, now()->addMinutes(5), function () use ($query) {
            return $query
                ->with(['category', 'specifications'])
                ->withCount(['items as items_count', 'items as available_items_count' => function ($q) {
                    $q->where('status', 'available');
                }])
                ->paginate(15)
                ->withQueryString();
        });

        $categories = Cache::remember('categories:all', now()->addMinutes(10), function () {
            return Category::all();
        });

        // Get unique brands for filter dropdown
        $brands = Cache::remember('brands:all', now()->addMinutes(10), function () {
            return Equipment::whereNotNull('brand')
                ->distinct()
                ->pluck('brand')
                ->sort()
                ->values();
        });


        // Get available specs for dynamic filtering
        $availableSpecs = Cache::remember('specs:all', now()->addMinutes(10), function () {
            return \App\Models\EquipmentSpecification::select('spec_key')
                ->distinct()
                ->pluck('spec_key')
                ->sort()
                ->values();
        });

        return view('home', [
            'equipments' => $equipments,
            'categories' => $categories,
            'brands' => $brands,
            'availableSpecs' => $availableSpecs,
        ]);
    }
}
