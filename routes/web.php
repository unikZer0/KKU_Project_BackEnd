<?php

//Admin
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BorrowTransactionController;
use App\Http\Controllers\NotificationController;

//Support 
use Illuminate\Support\Facades\Route;

//Borrower
use App\Http\Controllers\HomeController;
// use App\Http\Controllers\PublicEquipmentController;
// use App\Http\Controllers\Borrowers\BorrowController;
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
            Route::post('/store', [EquipmentController::class, 'store'])->name('equipment.store');
            Route::put('/update/{id}', [EquipmentController::class, 'update'])->name('equipment.update');
            Route::delete('/destroy/{id}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');
        });
    });

    Route::prefix('admin/category')->name('admin.')->group(function () {
        // Categories
        Route::get('/', [CategoryController::class, 'index'])->name('category.index');
        Route::post('/store', [CategoryController::class, 'store'])->name('category.store');
        Route::put('/update/{id}', [CategoryController::class, 'update'])->name('category.update');
        Route::delete('/destroy/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
    });

    Route::prefix('admin/transaction')->name('admin.')->group(function () {
        // Transactions
        Route::get('/', [BorrowTransactionController::class, 'index'])->name('transaction.index');
        Route::post('/checkin/{id}', [BorrowTransactionController::class, 'checkin'])->name('transaction.checkin');
        Route::post('/checkout/{id}', [BorrowTransactionController::class, 'checkout'])->name('transaction.checkout');
        Route::post('/store', [BorrowTransactionController::class, 'store'])->name('transaction.store');
    });

    //Requests
    Route::prefix('admin/requests')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'requestIndex'])->name('requests.index');
        Route::post('/{id}/approve', [AdminController::class, 'approveRequest'])->name('requests.approve');
        Route::post('/{id}/reject', [AdminController::class, 'rejectRequest'])->name('requests.reject');
    });

    // Reports
    Route::prefix('admin/report')->name('admin.')->group(function () {
        Route::get('/index', [AdminController::class, 'requestReport'])->name('report.index');
        Route::get('/users', [AdminController::class, 'userReport'])->name('report.users');
        Route::get('/equipments', [AdminController::class, 'equipmentReport'])->name('report.equipments');
        Route::get('/categories', [AdminController::class, 'categoryReport'])->name('report.categories');
    });
});

Route::middleware('can:staff')->group(function () {
    Route::get('/staff', function () {
        return view('home', ['section' => 'staff']);
    })->name('staff.index');
});

Route::middleware('can:borrower')->group(function () {
    Route::prefix('/borrower')->group(function () {
        Route::post('/borrow_request', [BorrowerCtrl::class, 'myRequests'])->name('borrower.borrow_request');
        Route::get('/myrequest', [BorrowerCtrl::class, 'myreq'])->name('borrower.equipments.myreq');
        Route::get('/reqdetail/{req_id}', [BorrowerCtrl::class, 'reqdetail'])->name('borrower.equipments.reqdetail');
        Route::patch('/borrower/requests/{id}/cancel', [BorrowerCtrl::class, 'cancel'])->name('borrower.requests.cancel');
    });
});

require __DIR__ . '/auth.php';
