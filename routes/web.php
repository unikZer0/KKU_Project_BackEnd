<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\EquipmentItemController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\BorrowRequestController;
use App\Http\Controllers\Admin\ReportExportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VerificationController;

use App\Http\Controllers\LogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Borrowers\BorrowerCtrl;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/equipments', [HomeController::class, 'index'])->name('equipments.index');
Route::get('/equipments/search', [BorrowerCtrl::class, 'search']);
Route::get('/equipments/all', [BorrowerCtrl::class, 'getAllEquipment']);
Route::get('/equipments/{code}', [BorrowerCtrl::class, 'show'])->name('equipments.show');

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Notifications
    Route::post('/notifications/mark-read/{id}', [NotificationController::class, 'markRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/request-verification', [ProfileController::class, 'requestVerification'])->name('profile.requestVerification');

    // Borrower area
    Route::prefix('/borrower')->group(function () {
        Route::post('/borrow_request', [BorrowerCtrl::class, 'myRequests'])->name('borrower.borrow_request');
        Route::get('/myrequest', [BorrowerCtrl::class, 'myreq'])->name('borrower.equipments.myreq');
        Route::get('/reqdetail/{req_id}', [BorrowerCtrl::class, 'reqdetail'])->name('borrower.equipments.reqdetail');
        // fixed path (prefix already includes /borrower)
        Route::patch('/requests/{id}/cancel', [BorrowerCtrl::class, 'cancel'])->name('borrower.requests.cancel');
    });

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        // Equipment management
        Route::prefix('admin/equipment')->group(function () {
            Route::post('/store', [EquipmentController::class, 'store'])->name('admin.equipment.store');
            Route::put('/update/{id}', [EquipmentController::class, 'update'])->name('admin.equipment.update');
            Route::delete('/destroy/{id}', [EquipmentController::class, 'destroy'])->name('admin.equipment.destroy');
        });

        // Equipment Items management
        Route::prefix('admin/equipment-items')->group(function () {
            Route::post('/store', [EquipmentItemController::class, 'store'])->name('admin.equipment-items.store');
            Route::put('/update/{id}', [EquipmentItemController::class, 'update'])->name('admin.equipment-items.update');
            Route::delete('/destroy/{id}', [EquipmentItemController::class, 'destroy'])->name('admin.equipment-items.destroy');
            Route::delete('/destroy-item/{equipmentId}/{itemId}', [EquipmentController::class, 'destroyItem'])->name('admin.equipment.destroy-item');
            Route::post('/bulk-update', [EquipmentItemController::class, 'bulkUpdate'])->name('admin.equipment-items.bulk-update');
        });

        // Category management
        Route::prefix('admin/category')->group(function () {
            Route::post('/store', [CategoryController::class, 'store'])->name('admin.category.store');
            Route::put('/update/{id}', [CategoryController::class, 'update'])->name('admin.category.update');
            Route::delete('/destroy/{id}', [CategoryController::class, 'destroy'])->name('admin.category.destroy');
        });

        // User management
        Route::prefix('admin/user')->group(function () {
            Route::post('/store', [UserController::class, 'store'])->name('admin.user.store');
            Route::put('/update/{id}', [UserController::class, 'update'])->name('admin.user.update');
            Route::delete('/destroy/{id}', [UserController::class, 'destroy'])->name('admin.user.destroy');
        });
        //     // Reports (subset)
        //     Route::prefix('admin/report')->group(function () {
        //         Route::get('/logs', [LogController::class, 'logReport'])->name('admin.report.logs');
        //     });
    });

    // Staff and Admin routes
    Route::middleware('role:admin,staff')->group(function () {
        // Admin dashboard
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

        // Full reports
        Route::prefix('admin/report')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('admin.report.index');
            Route::get('/vue/{type}', [ReportController::class, 'vueReport'])->name('admin.report.vue');
            Route::get('/export/{type}', [ReportExportController::class, 'export'])->name('admin.report.export');
        });

        // Requests
        Route::prefix('admin/requests')->group(function () {
            Route::get('/', [BorrowRequestController::class, 'index'])->name('admin.requests.index');
            Route::get('/{req_id}', [BorrowRequestController::class, 'show'])->name('admin.requests.show');
            Route::patch('/{req_id}', [BorrowRequestController::class, 'update'])->name('admin.requests.update');
            Route::match(['post', 'patch'], '/{req_id}/approve', [BorrowRequestController::class, 'approve'])->name('admin.requests.approve');
            Route::post('/{req_id}/reject', [BorrowRequestController::class, 'reject'])->name('admin.requests.reject');
            Route::post('/{req_id}/checkout', [BorrowRequestController::class, 'checkout'])->name('admin.requests.checkout');
        });

        // Category, Equipment, and User index pages
        Route::prefix('admin/category')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('admin.category.index');
        });
        Route::prefix('admin/equipment')->group(function () {
            Route::get('/', [EquipmentController::class, 'index'])->name('admin.equipment.index');
        });
        Route::prefix('admin/equipment-items')->group(function () {
            Route::get('/', [EquipmentItemController::class, 'index'])->name('admin.equipment-items.index');
            Route::get('/equipment/{equipmentId}', [EquipmentItemController::class, 'getByEquipment'])->name('admin.equipment-items.by-equipment');
           
        });
        Route::prefix('admin/user')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('admin.user.index');
        });
        // Verification routes
        Route::prefix('admin/verification')->group(function () {
            Route::get('/', [VerificationController::class, 'index'])->name('admin.verification.index');
            Route::get('/{id}', [VerificationController::class, 'show'])->name('admin.verification.show');
            Route::post('/{id}/approve', [VerificationController::class, 'approve'])->name('admin.verification.approve');
            Route::post('/{id}/reject', [VerificationController::class, 'reject'])->name('admin.verification.reject');
        });
    });
});

require __DIR__ . '/auth.php';
