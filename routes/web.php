<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\BorrowRequestController;
use App\Http\Controllers\Admin\ReportExportController;

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

        // Category management
        Route::prefix('admin/category')->group(function () {
            Route::post('/store', [CategoryController::class, 'store'])->name('admin.category.store');
            Route::put('/update/{id}', [CategoryController::class, 'update'])->name('admin.category.update');
            Route::delete('/destroy/{id}', [CategoryController::class, 'destroy'])->name('admin.category.destroy');
        });

        // Reports (subset)
        Route::prefix('admin/report')->group(function () {
            Route::get('/logs', [LogController::class, 'logReport'])->name('admin.report.logs');
        });
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
        });

        // Category and Equipment index pages
        Route::prefix('admin/category')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('admin.category.index');
        });
        Route::prefix('admin/equipment')->group(function () {
            Route::get('/', [EquipmentController::class, 'index'])->name('admin.equipment.index');
        });
    });
});

require __DIR__ . '/auth.php';
