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

        Route::prefix('/admin/borrow-request')->group(function () {
            Route::get('/', [BorrowRequestController::class, 'index'])->name('admin.borrow_request');
            Route::get('/add', [BorrowRequestController::class, 'add_request'])->name('admin.borrow_request.add');
            Route::patch('/{id}/approve', [BorrowRequestController::class, 'approve_request'])->name('admin.borrow_request.approve');
            Route::patch('/{id}/reject', [BorrowRequestController::class, 'reject_request'])->name('admin.borrow_request.reject');
            Route::post('/borrow-requests/{id}/checkout', [BorrowRequestController::class, 'checkout'])->name('admin.borrow_request.checkout');
            Route::post('/borrow-requests/{id}/checkin', [BorrowRequestController::class, 'checkin'])->name('admin.borrow_request.checkin');
        });
    });

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
});

require __DIR__ . '/auth.php';
