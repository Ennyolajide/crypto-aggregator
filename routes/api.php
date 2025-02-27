<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PriceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::get('/prices/latest', [PriceController::class, 'latest']);
    Route::get('/prices/{pair}/history', [PriceController::class, 'history']);
    // Route::get('/pairs', [PriceController::class, 'pairs']);
});
