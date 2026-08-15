<?php

use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\JourneyController;
use App\Http\Controllers\Admin\PeopleController;
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

/**
 * People routes (collection items of type "people").
 */
Route::controller(PeopleController::class)->prefix('people')->group(function () {
    Route::get('/', 'index')->name('admin.people.index');

    Route::get('create', 'create')->name('admin.people.create');

    Route::post('create', 'store')->name('admin.people.store');

    Route::get('edit/{id}', 'edit')->name('admin.people.edit');

    Route::put('edit/{id}', 'update')->name('admin.people.update');

    Route::delete('edit/{id}', 'destroy')->name('admin.people.destroy');
});

/**
 * Journey routes ("Join the Journey" form submissions).
 */
Route::controller(JourneyController::class)->prefix('journey')->group(function () {
    Route::get('/', 'index')->name('admin.journey.index');

    Route::delete('{id}', 'destroy')->name('admin.journey.destroy');
});
