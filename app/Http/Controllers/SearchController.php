<?php

namespace App\Http\Controllers;

use App\Services\SiteSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Site-wide search — the React app's SearchPage.
 *
 * Reads ?q= (or Bagisto's ?query=, since the storefront's search form still
 * posts here) and renders grouped matches across every collection, the stories
 * feed, the store and the games.
 */
class SearchController extends Controller
{
    public function __construct(protected SiteSearch $search) {}

    public function index(Request $request): View
    {
        $query = trim((string) ($request->query('q') ?? $request->query('query') ?? ''));

        return view('search.index', $this->search->search($query) + ['suggestions' => $this->popularQueries()]);
    }

    /**
     * Live suggestions for the header dropdown.
     *
     * Each row is rendered to HTML here so the browser doesn't need a copy of
     * the icon set — resources/js/app.js just injects the markup.
     */
    public function suggest(Request $request): JsonResponse
    {
        $query = trim((string) ($request->query('q') ?? $request->query('query') ?? ''));

        $suggestions = $query === '' ? [] : $this->search->suggestions($query, 7);

        return response()->json([
            'query' => $query,
            // url() treats a second argument as path segments, so build the query by hand.
            'resultsUrl' => url('/search').($query === '' ? '' : '?q='.rawurlencode($query)),
            'suggestions' => array_map(fn (array $suggestion) => [
                'title' => $suggestion['title'],
                'subtitle' => $suggestion['subtitle'],
                'label' => $suggestion['kind_label'],
                'url' => $suggestion['url'],
                'html' => view('partials.search-suggestion', ['suggestion' => $suggestion])->render(),
            ], $suggestions),
        ]);
    }

    /**
     * Example queries offered on the empty search page.
     *
     * @return array<int, string>
     */
    protected function popularQueries(): array
    {
        return ['Taj Mahal', 'Vivekananda', 'Ayurveda', 'Chess', 'Rangoli', 'Pachisi'];
    }
}
