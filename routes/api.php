<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Get authenticated user details
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public login route
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Logout authenticated user
    Route::post('/logout', [AuthController::class, 'logout']);

});