<?php

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OrderStatusController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(static function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::middleware('guest:api')->group(function () {
            Route::post('login', [AuthController::class, 'adminLogin'])->name('login');
            Route::post('create', [AdminController::class, 'store'])->name('create');
        });
        Route::middleware(['auth:api', 'admin'])->group(function () {
            Route::get('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('user-listing', [AdminController::class, 'index'])->name('index');
            Route::put('user-edit/{user:uuid}', [AdminController::class, 'update'])->name('update');
            Route::delete('user-delete/{user:uuid}', [AdminController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::middleware('guest:api')->group(function () {
            Route::post('login', [AuthController::class, 'login'])->name('login');
            Route::post('create', [UserController::class, 'store'])->name('create');
            Route::post('forgot-password', [AuthController::class, 'sendPasswordResetToken'])->name('forgot-password');
            Route::post('reset-password-token', [AuthController::class, 'resetPassword'])->name('reset-password');
        });
        Route::middleware('auth:api')->group(function () {
            Route::get('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/', [UserController::class, 'showSelf'])->name('showSelf');
            Route::put('edit', [UserController::class, 'updateSelf'])->name('updateSelf');
            Route::delete('/', [UserController::class, 'destroySelf'])->name('destroySelf');
            Route::get('orders', [UserController::class, 'orders'])->name('orders.index');
        });
    });

    Route::middleware('auth:api')->group(function () {
        //Order Statuses CRUD
        Route::get('order-statuses', [OrderStatusController::class, 'index'])->name('order-status.index');
        Route::prefix('order-status')->middleware('admin')->group(function () {
            Route::post('create', [OrderStatusController::class, 'store'])->name('order-status.store');
            Route::get('{orderStatus:uuid}', [OrderStatusController::class, 'show'])->name('order-status.show');
            Route::put('{orderStatus:uuid}', [OrderStatusController::class, 'update'])->name('order-status.update');
            Route::delete('{orderStatus:uuid}', [OrderStatusController::class, 'destroy'])->name('order-status.destroy');
        });
    });
});
