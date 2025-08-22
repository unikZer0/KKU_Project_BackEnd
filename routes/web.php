<?php

use App\Http\Controllers\Admin\adminCtrl;
use App\Http\Controllers\Admin\equitmentCtrl;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

// Route::get('//', function () {
//     return view('/');
// })->middleware(['auth', 'verified'])->name('/');

Route::middleware('auth')->group(function () {
    //api for auth

    // Role-based areas
    Route::middleware('can:admin')->group(function () {

        Route::get('/admin', [adminCtrl::class, 'index'])->name('admin.index');
        Route::get('/admin', [adminCtrl::class,'index'])->name('admin.index');
        Route::get('/equipment', [equitmentCtrl::class,'index'])->name('admin.equipment');
    });

    Route::middleware('can:staff')->group(function () {
        Route::get('/staff', function () {
            return view('home', ['section' => 'staff']);
        })->name('staff.index');
    });

    Route::middleware('can:borrower')->group(function () {
        Route::get('/borrower', function () {
            return view('home', ['section' => 'borrower']);
        })->name('borrower.index');
    });
});

require __DIR__ . '/auth.php';
