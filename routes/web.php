<?php

//Admin
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BorrowRequestController;

//Support 
use Illuminate\Support\Facades\Route;

//Borrower
use App\Http\Controllers\Borrowers\BorrowersRequestController;
use App\Http\Controllers\Borrowers\BorrowersHomeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicEquipmentController;
use App\Http\Controllers\BorrowController;

Route::get('/', [HomeController::class, 'index'])->name('home');
//show equipment details
Route::get('/equipments/{equipment:code}', [PublicEquipmentController::class, 'show'])->name('equipments.show');

// Route::get('//', function () {
//     return view('/');
// })->middleware(['auth', 'verified'])->name('/');

Route::middleware('auth')->group(function () {
    //api for auth

    // Role-based areas
    Route::middleware('can:admin')->group(function () {

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('index');
        });

        Route::prefix('admin')->name('admin.')->group(function () {
            // Equipments
            Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
            Route::get('/equipment/add', [EquipmentController::class, 'add_equipment'])->name('equipment.create');
            Route::post('/equipment/create', [EquipmentController::class, 'create_product'])->name('equipment.store');
            Route::get('/equipment/{id}/edit', [EquipmentController::class, 'edit_equipment'])->name('equipment.edit');
            Route::patch('/equipment/{id}', [EquipmentController::class, 'update_equipment'])->name('equipment.update');
            Route::delete('/equipment/{id}', [EquipmentController::class, 'delete_equipment'])->name('equipment.delete');
            // test
            Route::get('/test-upload', [EquipmentController::class, 'test_upload_form'])->name('equipment.test_upload');
        });
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        // Categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('category.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('category.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('category.store');
        Route::get('/categories/{id}', [CategoryController::class, 'edit'])->name('category.edit');
        Route::patch('/categories/{id}', [CategoryController::class, 'update'])->name('category.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('category.delete');
    });
});

// Route::prefix('/admin/borrow-request')->group(function () {
//     Route::get('/', [BorrowRequestController::class, 'index'])->name('admin.borrow_request');
//     Route::get('/add', [BorrowRequestController::class, 'add_request'])->name('admin.borrow_request.add');
//     Route::patch('/{id}/approve', [BorrowRequestController::class, 'approve_request'])->name('admin.borrow_request.approve');
//     Route::patch('/{id}/reject', [BorrowRequestController::class, 'reject_request'])->name('admin.borrow_request.reject');
//     Route::post('/borrow-requests/{id}/checkout', [BorrowRequestController::class, 'checkout'])->name('admin.borrow_request.checkout');
//     Route::post('/borrow-requests/{id}/checkin', [BorrowRequestController::class, 'checkin'])->name('admin.borrow_request.checkin');
// });


Route::middleware('can:staff')->group(function () {
    Route::get('/staff', function () {
        return view('home', ['section' => 'staff']);
    })->name('staff.index');
});

Route::middleware('can:borrower')->group(function () {

    Route::get('/borrower', [BorrowersHomeController::class, 'home'])->name('borrower.index');

    Route::prefix('/borrower/borrow-request')->group(function () {
        Route::get('/', [BorrowersRequestController::class, 'myRequests'])->name('borrower.borrow-request');
    });
});
//show equipment details
Route::get('/equipments/{id}', [PublicEquipmentController::class, 'show'])->name('equipments.show');
//borrow request
Route::post('/borrows', [BorrowController::class, 'store'])->name('borrows.store');




require __DIR__ . '/auth.php';
