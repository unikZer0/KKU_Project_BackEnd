<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Borrowers\BorrowersHomeController;
use App\Http\Controllers\HomeController;


Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::get('/category/{id}', function ($id) {
//     $category = Category::findOrFail($id);
//     return view('category.show', compact('category'))->name('category.show');
// });

// Route::get('//', function () {
//     return view('/');
// })->middleware(['auth', 'verified'])->name('/');

Route::middleware('auth')->group(function () {
    //api for auth

    // Role-based areas
    Route::middleware('can:admin')->group(function () {

        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

        Route::prefix('/admin/equipment')->group(function () {
            Route::get('/', [EquipmentController::class, 'index'])->name('admin.equipment');
            Route::get('/add', [EquipmentController::class, 'add_equipment'])->name('admin.equipment.add');

            //test 
            Route::get('/test-upload', [EquipmentController::class, 'test_upload_form'])->name('admin.equipment.test_upload');

            //
            Route::post('/upload', [EquipmentController::class, 'upload_product'])->name('admin.equipment.upload');
            Route::get('/{id}', [EquipmentController::class, 'edit_equipment'])->name('admin.equipment.edit');
            Route::delete('/{id}', [EquipmentController::class, 'delete_equipment'])->name('admin.equipment.delete');
        });

        Route::prefix('/admin/category')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('admin.catgory');
            Route::get('/add', [CategoryController::class, 'add_category'])->name('admin.category.add');
            Route::post('/{id}', [CategoryController::class, 'edit_category'])->name('admin.category.edit');
            Route::get('/{id}', [CategoryController::class, 'delete_category'])->name('admin.category.delete');
        });
    });

    Route::middleware('can:staff')->group(function () {
        Route::get('/staff', function () {
            return view('home', ['section' => 'staff']);
        })->name('staff.index');
    });

    Route::middleware('can:borrower')->group(function () {
        Route::get('/borrower', [BorrowersHomeController::class, 'home'])->name('borrower.index');
    });
    
});

require __DIR__ . '/auth.php';
