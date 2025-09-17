<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Equipment;

Route::get('/equipments', function () {
    return Equipment::all();
});
