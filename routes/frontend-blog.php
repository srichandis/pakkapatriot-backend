<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

/**
 * Blog frontend routes.
 * Declared before store-front fallback routes.
 */
Route::controller(BlogController::class)->prefix('blog')->group(function () {
    Route::get('/', 'index')->name('shop.blog.index');

    Route::get('{slug}', 'show')->name('shop.blog.show');
});
