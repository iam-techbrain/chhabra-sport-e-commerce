<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RazorpayController;
use App\Http\Controllers\Api\ContactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Endpoints
|--------------------------------------------------------------------------
*/

// Auth Endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Catalog Public Endpoints
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/categories', [ProductController::class, 'categories']);
Route::get('/brands', [ProductController::class, 'brands']);
Route::get('/tags', [ProductController::class, 'tags']);
Route::get('/attributes', [AttributeController::class, 'index']);

// Order Placement & Payment Endpoints (Public for Checkout)
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);
Route::post('/razorpay/create-order', [RazorpayController::class, 'createOrder']);
Route::post('/razorpay/verify-payment', [RazorpayController::class, 'verifyPayment']);

// Contact Us Public Submission
Route::post('/contact', [ContactController::class, 'store']);


/*
|--------------------------------------------------------------------------
| Sensitive / Admin Protected API Endpoints
|--------------------------------------------------------------------------
*/
Route::middleware(['admin.auth'])->group(function () {
    // User Accounts Management
    Route::get('/users', [AuthController::class, 'index']);

    // Admin Orders Management
    Route::get('/orders', [OrderController::class, 'index']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);

    // Admin Products Management
    Route::post('/products', [ProductController::class, 'store']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Admin Category Management
    Route::post('/categories', [ProductController::class, 'storeCategory']);
    Route::put('/categories/{id}', [ProductController::class, 'updateCategory']);
    Route::delete('/categories/{id}', [ProductController::class, 'destroyCategory']);

    // Admin Brand Management
    Route::post('/brands', [ProductController::class, 'storeBrand']);
    Route::put('/brands/{id}', [ProductController::class, 'updateBrand']);
    Route::delete('/brands/{id}', [ProductController::class, 'destroyBrand']);

    // Admin Tag Management
    Route::post('/tags', [ProductController::class, 'storeTag']);
    Route::put('/tags/{id}', [ProductController::class, 'updateTag']);
    Route::delete('/tags/{id}', [ProductController::class, 'destroyTag']);

    // Admin Attributes Management
    Route::post('/attributes', [AttributeController::class, 'store']);
    Route::post('/attributes/{id}/values', [AttributeController::class, 'addValue']);
    Route::delete('/attributes/{id}', [AttributeController::class, 'destroy']);
    Route::delete('/attribute-values/{id}', [AttributeController::class, 'destroyValue']);

    // Admin Contact Messages Management
    Route::get('/contact-messages', [ContactController::class, 'index']);
    Route::put('/contact-messages/{id}/status', [ContactController::class, 'updateStatus']);
    Route::delete('/contact-messages/{id}', [ContactController::class, 'destroy']);
});
