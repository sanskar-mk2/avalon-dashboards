<?php

use App\Http\Controllers\AccountReceivableController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OpenOrderController;
use App\Http\Controllers\SaleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['web', 'auth:sanctum'])->group(function () {
    Route::post('sales/check-existing', [SaleController::class, 'checkExistingData'])->name('sales.check-existing');
    Route::post('open_orders/check-existing', [OpenOrderController::class, 'checkExistingData'])->name('open_orders.check-existing');
    Route::post('account_receivables/check-existing', [AccountReceivableController::class, 'checkExistingData'])->name('account_receivables.check-existing');
    Route::post('inventories/check-existing', [InventoryController::class, 'checkExistingData'])->name('inventories.check-existing');
});
