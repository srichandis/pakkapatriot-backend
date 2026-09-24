<?php

namespace App\Http\Controllers;

use App\Services\BlogFeed;
use App\Services\ProductCatalog;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        protected BlogFeed $blogFeed,
        protected ProductCatalog $products,
    ) {}

    /**
     * The Pakka Patriot homepage: hero, pillars, What Pakka Loves, store,
     * latest stories and the newsletter block.
     */
    public function index(): View
    {
        return view('home', [
            'posts' => $this->safe(fn () => $this->blogFeed->latest(12), []),
            'products' => $this->safe(fn () => $this->products->all(50), []),
        ]);
    }

    /**
     * Render with a fallback so a database hiccup degrades to an empty state
     * instead of a 500 on the public homepage.
     */
    protected function safe(callable $callback, mixed $fallback): mixed
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            report($e);

            return $fallback;
        }
    }
}
