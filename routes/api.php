<?php

use App\Http\Controllers\Api\V1\AuthController;
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
    Route::prefix('user')->group(function () {
        Route::middleware('guest:api')->group(function () {
            Route::post('login', [AuthController::class, 'login']);
            Route::post('create', [UserController::class, 'store']);
        });
        Route::middleware('auth:api')->group(function () {
            Route::get('logout', [AuthController::class, 'logout']);
            Route::get('/', [UserController::class, 'showSelf']);
        });
    });
});
