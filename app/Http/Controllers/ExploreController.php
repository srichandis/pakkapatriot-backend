<?php

namespace App\Http\Controllers;

use App\Services\BlogFeed;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExploreController extends Controller
{
    public function __construct(protected BlogFeed $feed) {}

    /**
     * Explore: every story in one filterable grid.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));

        $known = collect($this->feed->categories())->pluck('value')->all();
        if (! in_array($category, $known, true)) {
            $category = '';
        }

        $posts = $this->feed->paginate(24, $search ?: null, $category ?: null);

        return view('explore.index', [
            'posts' => $posts,
            'categories' => collect($this->feed->categories())->sortBy('label')->values()->all(),
            'total' => $this->feed->publishedCount(),
            'search' => $search,
            'category' => $category,
        ]);
    }
}
