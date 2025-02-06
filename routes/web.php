<?php

use App\Http\Controllers\AccountReceivableController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OpenOrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalespersonController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return Inertia::render('Welcome', [
    //     'canLogin' => Route::has('login'),
    //     'canRegister' => Route::has('register'),
    //     'laravelVersion' => Application::VERSION,
    //     'phpVersion' => PHP_VERSION,
    // ]);
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('sales', SaleController::class);
    Route::delete('/sales', [SaleController::class, 'deleteAll'])->name('sales.deleteAll');
    Route::resource('open_orders', OpenOrderController::class);
    Route::delete('/open_orders', [OpenOrderController::class, 'deleteAll'])->name('open_orders.deleteAll');
    Route::resource('salespeople', SalespersonController::class);
    Route::delete('/salespeople', [SalespersonController::class, 'deleteAll'])->name('salespeople.deleteAll');
    Route::resource('locations', LocationController::class);
    Route::delete('/locations', [LocationController::class, 'deleteAll'])->name('locations.deleteAll');
    Route::resource('account_receivables', AccountReceivableController::class);
    Route::delete('/account_receivables', [AccountReceivableController::class, 'deleteAll'])->name('account_receivables.deleteAll');
    Route::resource('inventories', InventoryController::class);
    Route::delete('/inventories', [InventoryController::class, 'deleteAll'])->name('inventories.deleteAll');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

require __DIR__.'/auth.php';
