<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\CollectionItem;
use App\Models\Game;
use Illuminate\Support\Str;

/**
 * The single source of truth for site-wide search.
 *
 * Ported from the React app's `services/searchSite.ts`: ranks matches across
 * every knowledge collection (Ideas, Places, People, Culture, Create), the
 * stories feed, the merchandise store and the games, and returns them ordered
 * by relevance. Used by the /search results page and the header suggestions.
 */
class SiteSearch
{
    /**
     * Group metadata, in the order groups appear on the results page.
     */
    public const KINDS = [
        'ideas' => ['label' => 'Ideas', 'icon' => 'lightbulb'],
        'places' => ['label' => 'Places', 'icon' => 'map-pin'],
        'people' => ['label' => 'People', 'icon' => 'users'],
        'culture' => ['label' => 'Culture', 'icon' => 'palette'],
        'create' => ['label' => 'Create', 'icon' => 'sparkles'],
        'stories' => ['label' => 'Stories', 'icon' => 'file-text'],
        'store' => ['label' => 'Store', 'icon' => 'shopping-bag'],
        'games' => ['label' => 'Games', 'icon' => 'gamepad-2'],
    ];

    /**
     * Collection types that are searched, mapped to their public base path.
     */
    protected const COLLECTION_PATHS = [
        'ideas' => '/ideas',
        'places' => '/places',
        'people' => '/people',
        'culture' => '/culture',
        'create' => '/create',
    ];

    public function __construct(
        protected ProductCatalog $products,
        protected BlogFeed $blog,
    ) {}

    /**
     * Search the whole site.
     *
     * @return array{query: string, total: int, groups: array<int, array{kind: string, label: string, icon: string, count: int, items: array<int, array<string, mixed>>}>}
     */
    public function search(string $query, int $perGroup = 8): array
    {
        $q = mb_strtolower(trim($query));

        if ($q === '') {
            return ['query' => '', 'total' => 0, 'groups' => []];
        }

        $matches = array_merge(
            $this->matchCollections($q),
            $this->matchStories($q),
            $this->matchProducts($q),
            $this->matchGames($q),
        );

        usort($matches, function (array $a, array $b) {
            return $b['score'] <=> $a['score']
                ?: array_search($a['kind'], array_keys(self::KINDS)) <=> array_search($b['kind'], array_keys(self::KINDS))
                ?: mb_strlen($a['title']) <=> mb_strlen($b['title']);
        });

        $groups = [];
        $total = 0;

        foreach ($matches as $match) {
            $total++;
            $kind = $match['kind'];

            if (! isset($groups[$kind])) {
                $groups[$kind] = [
                    'kind' => $kind,
                    'label' => self::KINDS[$kind]['label'],
                    'icon' => self::KINDS[$kind]['icon'],
                    'items' => [],
                ];
            }

            // The group keeps every match's count, but only the first few are
            // rendered — matching the React results page.
            $groups[$kind]['count'] = ($groups[$kind]['count'] ?? 0) + 1;

            if (count($groups[$kind]['items']) < $perGroup) {
                unset($match['score']);
                $groups[$kind]['items'][] = $match;
            }
        }

        // Preserve the canonical kind order.
        $ordered = [];
        foreach (array_keys(self::KINDS) as $kind) {
            if (isset($groups[$kind])) {
                $ordered[] = $groups[$kind];
            }
        }

        return ['query' => $query, 'total' => $total, 'groups' => $ordered];
    }

    /**
     * Relevance score of a title against the query (higher = better).
     *
     * Prefers exact matches, then prefix + word-boundary matches (e.g. "taj" in
     * "Taj Mahal"), then whole-word matches anywhere (e.g. "kalam" in "A.P.J.
     * Abdul Kalam"), then loose prefix/word-prefix matches.
     */
    public function titleScore(string $title, string $q): int
    {
        $t = mb_strtolower($title);

        if ($t === $q) {
            return 100;
        }

        if (str_starts_with($t, $q)) {
            $next = mb_substr($t, mb_strlen($q), 1);

            // A word boundary right after the prefix makes the match stronger.
            return preg_match('/[a-z0-9]/', $next) ? 78 : 92;
        }

        $words = preg_split('/[\s\-–—\'’()\[\].,:;!?&\/+«»"]+/u', $t, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (in_array($q, $words, true)) {
            return 85;
        }

        foreach ($words as $word) {
            if (str_starts_with($word, $q)) {
                return 70;
            }
        }

        return 0;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function matchCollections(string $q): array
    {
        $matches = [];

        $items = CollectionItem::query()
            ->whereIn('type', array_keys(self::COLLECTION_PATHS))
            ->orderBy('name')
            ->get();

        foreach ($items as $item) {
            $coreIdeas = is_array($item->core_ideas) ? $item->core_ideas : [];
            $overview = is_array($item->overview) ? $item->overview : [];

            $body = mb_strtolower(implode(' ', array_filter(array_merge([
                $item->name,
                $item->native_name,
                $item->tagline,
                $item->category,
                $item->era,
                $item->attribution,
                $item->region,
                $item->summary,
                $item->legacy,
            ], $overview, array_map(
                fn ($idea) => trim(($idea['title'] ?? '').' '.($idea['text'] ?? '')),
                $coreIdeas
            )))));

            if (! str_contains($body, $q)) {
                continue;
            }

            $score = $this->titleScore((string) $item->name, $q);

            if (! $score && $item->native_name && str_contains(mb_strtolower($item->native_name), $q)) {
                $score = 55;
            }
            if (! $score && $item->tagline && str_contains(mb_strtolower($item->tagline), $q)) {
                $score = 40;
            }

            $matches[] = [
                'kind' => $item->type,
                'title' => $item->name,
                'subtitle' => $item->tagline ?? '',
                'category' => $item->category,
                'snippet' => $item->summary ?? '',
                'url' => url(self::COLLECTION_PATHS[$item->type].'/'.$item->slug),
                'image' => null,
                'score' => $score ?: 20,
            ];
        }

        return $matches;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function matchStories(string $q): array
    {
        $matches = [];

        foreach (Blog::published()->get() as $blog) {
            $category = $this->blogCategory($blog);

            $body = mb_strtolower(implode(' ', array_filter([
                $blog->title,
                strip_tags((string) $blog->excerpt),
                strip_tags((string) $blog->content),
                $category,
                $blog->author_name,
            ])));

            if (! str_contains($body, $q)) {
                continue;
            }

            $score = $this->titleScore((string) $blog->title, $q);

            if (! $score && $category && str_contains(mb_strtolower($category), $q)) {
                $score = 40;
            }

            $matches[] = [
                'kind' => 'stories',
                'title' => $blog->title,
                'subtitle' => trim($category.($blog->reading_time ? ' · '.$blog->reading_time.' min read' : ''), ' ·'),
                'category' => $category,
                'snippet' => Str::limit(strip_tags((string) $blog->excerpt), 160),
                'url' => url('/'.$blog->slug),
                'image' => null,
                'score' => $score ?: 20,
            ];
        }

        return $matches;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function matchProducts(string $q): array
    {
        $matches = [];

        foreach ($this->products->all(500) as $product) {
            $body = mb_strtolower(implode(' ', array_filter([
                $product['name'],
                $product['description'],
                $product['short_description'],
                $product['category'],
            ])));

            if (! str_contains($body, $q)) {
                continue;
            }

            $score = $this->titleScore((string) $product['name'], $q);

            if (! $score && $product['category'] && str_contains(mb_strtolower($product['category']), $q)) {
                $score = 40;
            }

            $matches[] = [
                'kind' => 'store',
                'title' => $product['name'],
                'subtitle' => $product['category'].' · ₹'.$product['price'],
                'category' => $product['category'],
                'snippet' => Str::limit($product['short_description'] ?: $product['description'], 160),
                'url' => url('/shop'),
                'image' => $product['image_url'],
                'product' => $product,
                'score' => $score ?: 20,
            ];
        }

        return $matches;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function matchGames(string $q): array
    {
        $matches = [];

        foreach (Game::orderBy('title')->get() as $game) {
            $tags = is_array($game->tags) ? $game->tags : (json_decode((string) $game->tags, true) ?: []);

            $body = mb_strtolower(implode(' ', array_filter(array_merge([
                $game->title,
                $game->tagline,
                $game->description,
                $game->badge,
            ], array_map(fn ($tag) => $tag['label'] ?? '', $tags)))));

            if (! str_contains($body, $q)) {
                continue;
            }

            $score = $this->titleScore((string) $game->title, $q);

            if (! $score && $game->tagline && str_contains(mb_strtolower($game->tagline), $q)) {
                $score = 40;
            }

            $matches[] = [
                'kind' => 'games',
                'title' => $game->title,
                'subtitle' => $game->tagline ?? '',
                'category' => $game->badge,
                'snippet' => $game->description ?? '',
                'url' => url($game->path),
                'image' => null,
                'score' => $score ?: 20,
            ];
        }

        return $matches;
    }

    /**
     * The uppercase badge shown for a post, matching BlogFeed.
     */
    protected function blogCategory(Blog $blog): string
    {
        $categories = ($blog->meta_data ?? [])['categories'] ?? [];
        $first = is_array($categories) ? reset($categories) : null;

        return is_string($first) && trim($first) !== '' ? mb_strtoupper(trim($first)) : 'STORIES';
    }

    /**
     * Suggestions for the header dropdown: titles only, in search order.
     *
     * @return array<int, array<string, mixed>>
     */
    public function suggestions(string $query, int $limit = 6): array
    {
        $flat = [];

        foreach ($this->search($query, $limit)['groups'] as $group) {
            foreach ($group['items'] as $item) {
                $flat[] = $item + ['kind_label' => $group['label'], 'kind_icon' => $group['icon']];
            }
        }

        return array_slice($flat, 0, $limit);
    }
}
