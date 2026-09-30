<?php

namespace App\Http\Controllers;

use App\Services\NewsFeed;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * NEWS — headlines about the country's achievements, collected from RSS.
 *
 * Filtering is server-side, like the blog and collection listings, so each
 * topic has its own linkable URL. The headlines come from NewsFeed's cache;
 * see app/Console/Commands/RefreshNews.php for how it is kept current.
 */
class NewsController extends Controller
{
    public function __construct(protected NewsFeed $feed) {}

    public function index(Request $request): View
    {
        $topic = trim((string) $request->query('topic', ''));
        $topics = $this->feed->topics();

        if (! in_array($topic, $topics, true)) {
            $topic = '';
        }

        $items = $this->feed->all();

        $visible = $topic === ''
            ? $items
            : array_values(array_filter($items, fn (array $item) => $item['topic'] === $topic));

        return view('news.index', [
            'items' => $visible,
            'topics' => $topics,
            'topic' => $topic,
            'total' => count($items),
            'refreshedAt' => $this->feed->refreshedAt(),
        ]);
    }
}
