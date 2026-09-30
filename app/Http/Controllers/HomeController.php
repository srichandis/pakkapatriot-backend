<?php

namespace App\Http\Controllers;

use App\Models\CollectionItem;
use App\Services\BlogFeed;
use App\Services\ProductCatalog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        protected BlogFeed $blogFeed,
        protected ProductCatalog $products,
    ) {}

    /**
     * The Pakka Patriot homepage: hero, pillars, latest stories, the games
     * showcase, the recent-picks columns, store and the newsletter block.
     */
    public function index(): View
    {
        return view('home', [
            'posts' => $this->safe(fn () => $this->blogFeed->latest(12), []),
            'products' => $this->safe(fn () => $this->products->all(50), []),
            'games' => $this->safe(fn () => PlayController::games(), []),
            'people' => $this->safe(fn () => $this->recent('people'), []),
            'places' => $this->safe(fn () => $this->recent('places'), []),
            'culture' => $this->safe(fn () => $this->recent('culture'), []),
        ]);
    }

    /**
     * The newest additions to a collection, for the homepage's pick columns.
     *
     * The id tiebreak keeps the pick stable: the seeded rows share a created_at,
     * so ordering on the timestamp alone would let the strip reshuffle.
     *
     * @return Collection<int, CollectionItem>
     */
    protected function recent(string $type): Collection
    {
        return CollectionItem::ofType($type)
            ->latest()
            ->orderByDesc('id')
            ->limit(4)
            ->get();
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
