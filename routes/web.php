<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetMovementController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Asset Management (Read: All staff, Write: Administrator & IT Staff)
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::middleware('role:administrator,it_staff')->group(function () {
        Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
        Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
        Route::patch('/assets/{asset}/retire', [AssetController::class, 'retire'])->name('assets.retire');
        Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
    });
    Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
    // Operations Workflows (Read: All staff, Write: Administrator & IT Staff)
    Route::get('/movements', [AssetMovementController::class, 'index'])->name('movements.index');
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::middleware('role:administrator,it_staff')->group(function () {
        // Asset Movements (FR-04)
        Route::get('/movements/create', [AssetMovementController::class, 'create'])->name('movements.create');
        Route::post('/movements', [AssetMovementController::class, 'store'])->name('movements.store');

        // Loans & Returns (FR-05)
        Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
        Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
        Route::get('/loans/{loan}/return', [LoanController::class, 'returnForm'])->name('loans.return');
        Route::post('/loans/{loan}/return', [LoanController::class, 'returnProcess'])->name('loans.return.process');

        // Maintenance (FR-06)
        Route::get('/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
        Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
        Route::get('/maintenance/{maintenance}/complete', [MaintenanceController::class, 'completeForm'])->name('maintenance.complete');
        Route::post('/maintenance/{maintenance}/complete', [MaintenanceController::class, 'completeProcess'])->name('maintenance.complete.process');
    });

    // Admin-Only Routes
    Route::middleware('role:administrator')->group(function () {
        // Users & Roles (FR-003)
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::resource('users', UserController::class)->except(['destroy', 'show']);

        // Master Data (FR-005, FR-006, FR-007)
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('locations', LocationController::class)->except(['show']);
        Route::resource('departments', DepartmentController::class)->except(['show']);

        // Audit Trail (FR-041 - FR-044)
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });
});
