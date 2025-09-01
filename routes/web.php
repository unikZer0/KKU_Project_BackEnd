<?php

//Admin
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\NotificationController;

//Support 
use Illuminate\Support\Facades\Route;

//Borrower
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicEquipmentController;
use App\Http\Controllers\Borrowers\BorrowController;
use App\Http\Controllers\Borrowers\BorrowerCtrl;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/equipments', [HomeController::class, 'index'])->name('equipments.index');
//search 
Route::get('/equipments/search', [BorrowerCtrl::class, 'search']);
Route::get('/equipments/all', [BorrowerCtrl::class, 'getAllEquipment']);
//show equipment details
Route::get('/equipments/{code}', [BorrowerCtrl::class, 'show'])->name('equipments.show');
//mark read


Route::middleware('auth')->group(function () {
    // routes/web.php
Route::post('/notifications/mark-read/{id}', [NotificationController::class, 'markRead']);
Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
    // Role-based areas
    Route::middleware('can:admin')->group(function () {

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('index');
        });

        Route::prefix('admin/equipment')->name('admin.')->group(function () {
            // Equipments
            Route::get('/', [EquipmentController::class, 'index'])->name('equipment.index');
            Route::get('/create', [EquipmentController::class, 'create'])->name('equipment.create');
            Route::post('/store', [EquipmentController::class, 'store'])->name('equipment.store');
            Route::get('/edit/{id}', [EquipmentController::class, 'edit'])->name('equipment.edit');
            Route::put('/update/{id}', [EquipmentController::class, 'update'])->name('equipment.update');
            Route::delete('/destroy/{id}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');
        });
    });

    Route::prefix('admin/category')->name('admin.')->group(function () {
        // Categories
        Route::get('/', [CategoryController::class, 'index'])->name('category.index');
        Route::get('/create', [CategoryController::class, 'create'])->name('category.create');
        Route::post('/store', [CategoryController::class, 'store'])->name('category.store');
        Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');
        Route::put('/update/{id}', [CategoryController::class, 'update'])->name('category.update');
        Route::delete('/destroy/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
    });
    //Requests
    Route::get('/admin/requests', [AdminController::class, 'requestIndex'])->name('admin.requests.index');
    Route::post('/admin/requests/{id}/approve', [AdminController::class, 'approveRequest'])->name('admin.requests.approve');
    Route::post('/admin/requests/{id}/reject', [AdminController::class, 'rejectRequest'])->name('admin.requests.reject');
    
    // Reports
    Route::get('/admin/report/index', [AdminController::class, 'requestReport'])->name('admin.report.index');
    Route::get('/admin/report/users', [AdminController::class, 'userReport'])->name('admin.report.users');
    Route::get('/admin/report/equipments', [AdminController::class, 'equipmentReport'])->name('admin.report.equipments');
    Route::get('/admin/report/categories', [AdminController::class, 'categoryReport'])->name('admin.report.categories');

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
    Route::prefix('/borrower')->group(function () {
        Route::post('/borrow_request', [BorrowerCtrl::class, 'myRequests'])->name('borrower.borrow_request');
        Route::get('/myreq', [BorrowerCtrl::class, 'myreq'])->name('borrower.equipments.myreq');
        Route::patch('/borrower/requests/{id}/cancel', [BorrowerCtrl::class, 'cancel'])->name('borrower.requests.cancel');

    });
});

require __DIR__ . '/auth.php';
