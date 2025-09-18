<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Equipment;
use App\Models\Category;
use App\Models\User;

Route::get('/equipments', function () {
    return Equipment::with('category')->get();
});
Route::get('/categories', function () {
    $categories = Category::withCount('equipments')->get();
    return response()->json($categories);
});
Route::get('/users', function () {
    return user::all();
});
