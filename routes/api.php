<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\UsageController;
use App\Http\Controllers\Api\InvoiceController;


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

    // Subscription routes
    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel']);
    Route::post('/subscriptions/{subscription}/change-plan', [SubscriptionController::class, 'changePlan']);

    // Usage Events
    Route::post('/usage', [UsageController::class, 'store']);


    // Invoice Routes
    Route::post('/subscriptions/{subscription}/invoice', [InvoiceController::class, 'generate']);
    Route::get('/invoices', [InvoiceController::class, 'index']);

    // Read Single Invoice
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);
    

});