<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

// Route::get('//', function () {
//     return view('/');
// })->middleware(['auth', 'verified'])->name('/');

Route::middleware('auth')->group(function () {
    //api for auth
    //here
    // Role-based areas
    Route::middleware('can:admin')->group(function () {
        Route::get('/admin', function () {
            return view('home', ['section' => 'admin']);
        })->name('admin.index');
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
