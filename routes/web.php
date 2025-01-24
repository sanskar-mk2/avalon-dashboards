<?php

use App\Http\Controllers\OpenOrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalespersonController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
});

require __DIR__ . '/auth.php';
