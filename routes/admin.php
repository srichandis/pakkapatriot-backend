<?php

use App\Http\Controllers\Admin\AmazonProductController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CreateController;
use App\Http\Controllers\Admin\CultureController;
use App\Http\Controllers\Admin\GamesController;
use App\Http\Controllers\Admin\IdeasController;
use App\Http\Controllers\Admin\JourneyController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\PeopleController;
use App\Http\Controllers\Admin\PlacesController;
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
 * Ideas routes (collection items of type "ideas").
 */
Route::controller(IdeasController::class)->prefix('ideas')->group(function () {
    Route::get('/', 'index')->name('admin.ideas.index');

    Route::get('create', 'create')->name('admin.ideas.create');

    Route::post('create', 'store')->name('admin.ideas.store');

    Route::get('edit/{id}', 'edit')->name('admin.ideas.edit');

    Route::put('edit/{id}', 'update')->name('admin.ideas.update');

    Route::delete('edit/{id}', 'destroy')->name('admin.ideas.destroy');
});

/**
 * Places routes (collection items of type "places").
 */
Route::controller(PlacesController::class)->prefix('places')->group(function () {
    Route::get('/', 'index')->name('admin.places.index');

    Route::get('create', 'create')->name('admin.places.create');

    Route::post('create', 'store')->name('admin.places.store');

    Route::get('edit/{id}', 'edit')->name('admin.places.edit');

    Route::put('edit/{id}', 'update')->name('admin.places.update');

    Route::delete('edit/{id}', 'destroy')->name('admin.places.destroy');
});

/**
 * Culture routes (collection items of type "culture").
 */
Route::controller(CultureController::class)->prefix('culture')->group(function () {
    Route::get('/', 'index')->name('admin.culture.index');

    Route::get('create', 'create')->name('admin.culture.create');

    Route::post('create', 'store')->name('admin.culture.store');

    Route::get('edit/{id}', 'edit')->name('admin.culture.edit');

    Route::put('edit/{id}', 'update')->name('admin.culture.update');

    Route::delete('edit/{id}', 'destroy')->name('admin.culture.destroy');
});

/**
 * Create routes (collection items of type "create").
 */
Route::controller(CreateController::class)->prefix('create')->group(function () {
    Route::get('/', 'index')->name('admin.create.index');

    Route::get('create', 'create')->name('admin.create.create');

    Route::post('create', 'store')->name('admin.create.store');

    Route::get('edit/{id}', 'edit')->name('admin.create.edit');

    Route::put('edit/{id}', 'update')->name('admin.create.update');

    Route::delete('edit/{id}', 'destroy')->name('admin.create.destroy');
});

/**
 * Games routes.
 */
Route::controller(GamesController::class)->prefix('games')->group(function () {
    Route::get('/', 'index')->name('admin.games.index');

    Route::get('create', 'create')->name('admin.games.create');

    Route::post('create', 'store')->name('admin.games.store');

    Route::get('edit/{id}', 'edit')->name('admin.games.edit');

    Route::put('edit/{id}', 'update')->name('admin.games.update');

    Route::delete('edit/{id}', 'destroy')->name('admin.games.destroy');
});

/**
 * Amazon affiliate product routes.
 */
Route::controller(AmazonProductController::class)->prefix('amazon-products')->group(function () {
    Route::get('/', 'index')->name('admin.amazon-products.index');

    Route::get('create', 'create')->name('admin.amazon-products.create');

    Route::post('create', 'store')->name('admin.amazon-products.store');

    Route::get('edit/{id}', 'edit')->name('admin.amazon-products.edit');

    Route::put('edit/{id}', 'update')->name('admin.amazon-products.update');

    Route::delete('edit/{id}', 'destroy')->name('admin.amazon-products.destroy');
});

/**
 * Journey routes ("Join the Journey" form submissions).
 */
Route::controller(JourneyController::class)->prefix('journey')->group(function () {
    Route::get('/', 'index')->name('admin.journey.index');

    Route::delete('{id}', 'destroy')->name('admin.journey.destroy');
});

/**
 * Newsletter routes ("Let's stay in touch!" form subscriptions).
 */
Route::controller(NewsletterController::class)->prefix('newsletter')->group(function () {
    Route::get('/', 'index')->name('admin.newsletter.index');

    Route::delete('{id}', 'destroy')->name('admin.newsletter.destroy');
});
