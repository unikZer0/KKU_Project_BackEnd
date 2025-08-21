<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\EquipmentsController;
use App\Http\Controllers\api\CategoriesController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource("equipments", EquipmentsController::class)->only(['index', 'show']);
Route::apiResource("categories", CategoriesController::class);
