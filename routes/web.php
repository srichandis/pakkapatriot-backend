<?php

use App\Http\Controllers\CollectionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

$collectionTypes = 'ideas|places|people|culture|create';

Route::get('/{type}', [CollectionController::class, 'browse'])
    ->where('type', $collectionTypes)
    ->name('collection.browse');

Route::get('/{type}/{slug}', [CollectionController::class, 'show'])
    ->where('type', $collectionTypes)
    ->name('collection.show');

Route::get('/games', [CollectionController::class, 'games'])->name('games');
Route::get('/ebooks', [CollectionController::class, 'ebooks'])->name('ebooks');
Route::get('/activities', [CollectionController::class, 'activities'])->name('activities');
