<?php

use App\Http\Controllers\Api\BlogApiController;
use App\Http\Controllers\Api\CollectionApiController;
use App\Http\Controllers\Api\JourneyApiController;
use App\Http\Controllers\Api\OrderApiController;
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

Route::post('/orders', [OrderApiController::class, 'store']);

Route::post('/join-journey', [JourneyApiController::class, 'store']);

Route::get('/data', [CollectionApiController::class, 'data']);
