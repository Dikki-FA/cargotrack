<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShipmentController;
use App\Http\Controllers\Api\TrackingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/shipments/{tracking_number}', [TrackingController::class, 'show'])->middleware('throttle:30,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/shipments', [ShipmentController::class, 'index']);
        Route::post('/shipments', [ShipmentController::class, 'store']);
        Route::post('/shipments/estimate', [ShipmentController::class, 'estimate']);
        Route::patch('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus']);
        Route::get('/shipments/{shipment}/label', [ShipmentController::class, 'label']);
    });
});