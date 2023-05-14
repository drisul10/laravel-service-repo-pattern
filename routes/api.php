<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\VehicleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('jwt.auth');
});

Route::post('sale', [SaleController::class, 'save'])->middleware('jwt.auth');
Route::get('sales', [SaleController::class, 'getManyWithPagination'])->middleware('jwt.auth');

Route::post('vehicle', [VehicleController::class, 'save'])->middleware('jwt.auth');
Route::get('vehicles', [VehicleController::class, 'getManyWithPagination'])->middleware('jwt.auth');
