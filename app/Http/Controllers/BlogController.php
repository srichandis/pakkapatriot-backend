<?php

namespace App\Http\Controllers;

use App\Services\BlogFeed;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Webkul\Shop\Http\Controllers\ProductsCategoriesProxyController;

class BlogController extends Controller
{
    public function __construct(protected BlogFeed $feed) {}

    /**
     * Story listing with server-side search and category filtering.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));

        return view('blog.index', [
            'posts' => $this->feed->paginate(24, $search ?: null, $category ?: null),
            'categories' => $this->feed->categories(),
            'total' => $this->feed->publishedCount(),
            'search' => $search,
            'category' => $category,
        ]);
    }

    /**
     * Display the specified blog post.
     *
     * Blog permalinks live at the root ({slug}). If the slug is not a
     * published blog post, fall back to the storefront proxy so legacy
     * category/product URLs at the root keep working.
     */
    public function show(string $slug)
    {
        $post = $this->feed->find($slug);

        if (! $post) {
            return app(ProductsCategoriesProxyController::class)->index(request());
        }

        return view('blog.show', compact('post'));
    }
}
