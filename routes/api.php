<?php

use App\Http\Controllers\Api\AmazonProductApiController;
use App\Http\Controllers\Api\BlogApiController;
use App\Http\Controllers\Api\CollectionApiController;
use App\Http\Controllers\Api\JourneyApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PakkaPatriot API Routes
|--------------------------------------------------------------------------
|
| Public API endpoints consumed by the Vite React frontend.
|
*/

Route::get('/blogs', [BlogApiController::class, 'index']);
Route::get('/blogs/{slug}', [BlogApiController::class, 'show']);

Route::get('/shop/products', [ProductApiController::class, 'index']);
Route::get('/shop/products/{id}', [ProductApiController::class, 'show']);

Route::get('/amazon-products', [AmazonProductApiController::class, 'index']);

Route::post('/orders', [OrderApiController::class, 'store']);

// Razorpay payment flow (Flutter app checkout).
Route::post('/payments/init', [PaymentApiController::class, 'init']);
Route::get('/payments/callback', [PaymentApiController::class, 'callback'])
    ->name('api.payments.callback');
Route::post('/payments/webhook', [PaymentApiController::class, 'webhook']);
Route::get('/payments/status', [PaymentApiController::class, 'status']);

Route::post('/join-journey', [JourneyApiController::class, 'store']);

Route::get('/data', [CollectionApiController::class, 'data']);
