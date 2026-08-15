<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

/**
 * Blog frontend routes.
 *
 * Declared after the store-front/customer/checkout routes (see
 * packages/Webkul/Shop/src/Routes/web.php) so the root-level post permalink
 * route below does not shadow single-segment storefront URLs like /cart,
 * /checkout, /search, /contact-us, /sitemap.xml etc.
 */
Route::controller(BlogController::class)->group(function () {
    // Blog listing (legacy web view).
    Route::get('blog', 'index')->name('shop.blog.index');

    // Old /blog/{slug} permalinks permanently redirect to the new root permalink.
    Route::permanentRedirect('blog/{slug}', '/{slug}');

    // Blog post permalinks now live at the root: /{slug}
    Route::get('{slug}', 'show')->name('shop.blog.show');
});
