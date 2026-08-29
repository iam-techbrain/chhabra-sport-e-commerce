<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RazorpayController;
use App\Http\Controllers\Api\ContactController;
use Illuminate\Support\Facades\Route;

// Auth Endpoints (Persisted in DB users table)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/users', [AuthController::class, 'index']);

// Product Endpoints
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
// Category Endpoints
Route::get('/categories', [ProductController::class, 'categories']);
Route::post('/categories', [ProductController::class, 'storeCategory']);
Route::put('/categories/{id}', [ProductController::class, 'updateCategory']);
Route::delete('/categories/{id}', [ProductController::class, 'destroyCategory']);

// Brand Endpoints
Route::get('/brands', [ProductController::class, 'brands']);
Route::post('/brands', [ProductController::class, 'storeBrand']);
Route::put('/brands/{id}', [ProductController::class, 'updateBrand']);
Route::delete('/brands/{id}', [ProductController::class, 'destroyBrand']);

// Tag Endpoints
Route::get('/tags', [ProductController::class, 'tags']);
Route::post('/tags', [ProductController::class, 'storeTag']);
Route::put('/tags/{id}', [ProductController::class, 'updateTag']);
Route::delete('/tags/{id}', [ProductController::class, 'destroyTag']);

// Attribute & Variable Endpoints (Database Persisted)
Route::get('/attributes', [AttributeController::class, 'index']);
Route::post('/attributes', [AttributeController::class, 'store']);
Route::post('/attributes/{id}/values', [AttributeController::class, 'addValue']);
Route::delete('/attributes/{id}', [AttributeController::class, 'destroy']);
Route::delete('/attribute-values/{id}', [AttributeController::class, 'destroyValue']);

// Order Endpoints
Route::get('/orders', [OrderController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);
Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);

// Razorpay Payment Endpoints
Route::post('/razorpay/create-order', [RazorpayController::class, 'createOrder']);
Route::post('/razorpay/verify-payment', [RazorpayController::class, 'verifyPayment']);

// Contact Us Form Endpoints (Saved to DB)
Route::post('/contact', [ContactController::class, 'store']);
Route::get('/contact-messages', [ContactController::class, 'index']);
Route::put('/contact-messages/{id}/status', [ContactController::class, 'updateStatus']);
Route::delete('/contact-messages/{id}', [ContactController::class, 'destroy']);


