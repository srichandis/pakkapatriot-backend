<?php

use App\Http\Controllers\Admin\BlogController;
use Illuminate\Support\Facades\Route;

/**
 * Blog routes.
 */
Route::controller(BlogController::class)->prefix('blogs')->group(function () {
    Route::get('/', 'index')->name('admin.blogs.index');

    Route::get('create', 'create')->name('admin.blogs.create');

    Route::post('create', 'store')->name('admin.blogs.store');

    Route::get('edit/{id}', 'edit')->name('admin.blogs.edit');

    Route::put('edit/{id}', 'update')->name('admin.blogs.update');

    Route::delete('edit/{id}', 'destroy')->name('admin.blogs.destroy');
});
