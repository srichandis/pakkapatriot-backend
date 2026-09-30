<?php

use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ComingSoonController;
use App\Http\Controllers\CreateController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\EBooksController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PlayController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Pakka Patriot front-end pages (Blade)
|--------------------------------------------------------------------------
|
| These are the public pages of the site — the ones the Vite/React front-end
| used to serve. They live in their own file because of route ordering:
|
| 1. The Bagisto Shop package registers its storefront routes (including the
|    `shop.product_or_category.index` fallback) while the app boots.
| 2. `routes/frontend-blog.php` is loaded *after* that, and claims `GET /{slug}`
|    for root-level blog permalinks — a single-segment catch-all.
|
| Any single-segment page (`/people`, `/games`, `/search`, …) must be declared
| before that catch-all or the blog route wins and 404s. So this file is
| required from `frontend-blog.php`, immediately above the permalink route.
|
| The home route also takes over `GET /`: Laravel keeps one route per URI, so
| the Shop package's `shop.home.index` name disappears with it. That name is
| linked from Bagisto's shared error pages and breadcrumbs, so it is re-aliased
| to a redirect below.
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

if (! Route::has('shop.home.index')) {
    Route::redirect('/home', '/')->name('shop.home.index');
}

$collectionTypes = 'ideas|places|people|culture';

Route::get('/{type}', [CollectionController::class, 'browse'])
    ->where('type', $collectionTypes)
    ->name('collection.browse');

Route::get('/{type}/{slug}', [CollectionController::class, 'show'])
    ->where('type', $collectionTypes)
    ->name('collection.show');

/*
| CREATE — the maker's space, plus the "Create — Made in Bhārat" creations.
|
| React routed /create to the maker's space and /create/{slug} to a creation's
| detail page, with no index for the creations themselves. This app keeps that
| shape and adds the missing index at /create/creations, so the Creations
| collection is still browsable.
*/
Route::get('/create', [CreateController::class, 'index'])->name('create');
Route::get('/create/creations', [CollectionController::class, 'browseCreate'])->name('collection.browse.create');
Route::get('/create/activity/{slug}', [CreateController::class, 'activity'])->name('create.activity');
// Must stay last: /create/{slug} would otherwise swallow /create/creations.
Route::get('/create/{slug}', [CollectionController::class, 'showCreate'])->name('collection.show.create');

/*
| PLAY — the games index and the five playable boards.
|
| The game pages use layouts.game (no site header/footer) and frame the HTML
| app from public/{board}/index.html.
*/
Route::get('/play', [PlayController::class, 'index'])->name('play');
Route::get('/play/{game}', [PlayController::class, 'show'])->name('play.game');

// The old games/activities listings predate /play and the maker's space.
Route::permanentRedirect('/games', '/play');
Route::permanentRedirect('/activities', '/create');

/*
| The merchandise store — Made in Bhārat.
|
| Declared before the blog permalink catch-all, so `/shop` is not treated as a
| post slug.
*/
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
// NOTE: the parameter must not be named `category` — Bagisto registers a
// route-model binder for {category} that resolves Webkul\Category\Models\Category
// and 404s on everything else.
Route::get('/shop/{categorySlug}', [ShopController::class, 'category'])->name('shop.category');
Route::permanentRedirect('/made-in-india', '/shop');

/*
| Static and library pages.
*/

Route::view('/about', 'about')->name('about');

Route::get('/privacy', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/terms', [LegalController::class, 'terms'])->name('legal.terms');

Route::get('/explore', [ExploreController::class, 'index'])->name('explore');

// Temple Digital Panchangam — the astronomy runs in the browser
// (resources/js/panchanga.js), so this is a static shell.
Route::view('/panchangam', 'panchanga.index')->name('panchangam');

// The free eBook library. `/stories` is the public URL the React front-end
// used; `/ebooks` was the half-built Laravel URL, kept as a permanent redirect
// (and as a route name, since the legacy Blade layout links to it).
Route::get('/stories', [EBooksController::class, 'index'])->name('stories');
Route::permanentRedirect('/ebooks', '/stories')->name('ebooks');

// The React front-end listed blog posts under /blogs; /blog is the Laravel URL.
Route::permanentRedirect('/blogs', '/blog');

/*
| NEWS — headlines about the country's achievements.
|
| Collected from the RSS topics in config/news.php and cached; refreshed
| daily by `news:refresh`.
*/
Route::get('/news', [NewsController::class, 'index'])->name('news');

/*
| DOWNLOADS — the printable library.
|
| The eleven material types are catalogued in config/downloads.php. Both the
| hub and its category pages sit above the blog permalink catch-all, since
| they are single-segment paths like any other page here.
*/
Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads');
Route::get('/downloads/{category}', [DownloadController::class, 'show'])->name('downloads.category');

/*
| COMING SOON — For Teachers, Apps and Resources.
|
| These three secondary-nav entries are announced but not built yet, so each
| gets its own holding page. They used to point at pages that do exist
| (/explore and /play); those pages stay reachable from the footer ("Explore",
| "Fun Zone") and the header's GAMES entry, so only the nav links changed.
*/
Route::get('/for-teachers', [ComingSoonController::class, 'teachers'])->name('coming-soon.teachers');
Route::get('/apps', [ComingSoonController::class, 'apps'])->name('coming-soon.apps');
Route::get('/resources', [ComingSoonController::class, 'resources'])->name('coming-soon.resources');

/*
| Site-wide search — replaces Bagisto's storefront search page.
|
| The route keeps Bagisto's `shop.search.index` name: the storefront's shared
| header (still rendered by cart, checkout, customer and contact pages) builds
| its search form action from that name, so dropping it would 500 those pages.
| Naming it `search` instead would need a second route on the same URI, and
| Laravel only keeps the last one — so `url('/search')` is the canonical URL
| used across the Blade views. The page accepts ?q= and the storefront form's
| ?query=.
*/
Route::get('/search', [SearchController::class, 'index'])->name('shop.search.index');

// Live suggestions for the header dropdown (JSON).
Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');
