<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\EquipmentsController;
use App\Http\Controllers\api\CategoriesController;
use App\Models\Equipment;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::apiResource("equipments", EquipmentsController::class);
// Route::apiResource("categories", CategoriesController::class);
Route::apiResource("equipments", EquipmentsController::class);
Route::apiResource("categories", CategoriesController::class);
Route::get('/equipments', function () {
    return Equipment::all();
});
