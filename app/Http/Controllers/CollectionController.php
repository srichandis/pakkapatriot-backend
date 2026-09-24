<?php

namespace App\Http\Controllers;

use App\Models\CollectionItem;
use App\Support\Gradient;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CollectionController extends Controller
{
    /**
     * Metadata for each browsable collection (mirrors the React app's Collection registry).
     */
    public array $collections = [
        'ideas' => [
            'navLabel' => 'IDEAS',
            'heroIcon' => 'Lightbulb',
            'badgeLabel' => 'Ideas of Bhārat',
            'titlePrefix' => 'Philosophies born in',
            'titleHighlight' => 'Bhārat',
            'subtitle' => 'From the atom-dreaming sage Kanada to the compassion of the Buddha, from the logic of Nyaya to the love of the Bhakti saints — for over 3,000 years, Bhārat has been the birthplace of the world\'s boldest questions. Explore the great schools of thought that began on this soil.',
            'searchPlaceholder' => 'Search philosophies, founders, or ideas...',
            'itemNoun' => 'philosophies',
            'itemNounSingular' => 'philosophy',
            'categories' => [
                ['id' => 'Vedic', 'label' => 'Vedic Schools'],
                ['id' => 'Śramaṇa', 'label' => 'Śramaṇa (Non-Vedic)'],
                ['id' => 'Devotional', 'label' => 'Bhakti & Devotion'],
                ['id' => 'Esoteric', 'label' => 'Esoteric'],
            ],
            'eraLabel' => 'Period',
            'attributionLabel' => 'Founder',
            'regionLabel' => 'Birthplace',
            'categoryLabel' => 'Tradition',
            'groupByCategory' => true,
        ],
        'places' => [
            'navLabel' => 'PLACES',
            'heroIcon' => 'MapPin',
            'badgeLabel' => 'Places of Bhārat',
            'titlePrefix' => 'Wonders',
            'titleHighlight' => 'of Bhārat',
            'subtitle' => 'From the snows of the Taj to the palms of Kerala, from temples carved out of mountains to cities older than legend — explore the places that make Bhārat the world\'s most extraordinary land.',
            'searchPlaceholder' => 'Search places, cities, or monuments...',
            'itemNoun' => 'places',
            'itemNounSingular' => 'place',
            'categories' => [
                ['id' => 'Monuments & Forts', 'label' => 'Monuments & Forts'],
                ['id' => 'Ancient Marvels', 'label' => 'Ancient Marvels'],
                ['id' => 'Spiritual Sites', 'label' => 'Spiritual Sites'],
                ['id' => 'Natural Wonders', 'label' => 'Natural Wonders'],
                ['id' => 'Modern Landmarks', 'label' => 'Modern Landmarks'],
                ['id' => 'Patriotic Places', 'label' => 'Patriotic Places'],
            ],
            'eraLabel' => 'Era',
            'attributionLabel' => 'Built by',
            'regionLabel' => 'Location',
            'categoryLabel' => 'Type',
            'groupByCategory' => true,
        ],
        'people' => [
            'navLabel' => 'PEOPLE',
            'heroIcon' => 'Users',
            'badgeLabel' => 'People of Bhārat',
            'titlePrefix' => 'Icons',
            'titleHighlight' => 'of Bhārat',
            'subtitle' => 'The freedom fighters, scientists, poets, saints, kings, artists, and champions who shaped Bhārat — from the heroes of the epics to the father of the nation. Meet the people who made the story of Bhārat.',
            'searchPlaceholder' => 'Search icons, fields, or eras...',
            'itemNoun' => 'icons',
            'itemNounSingular' => 'icon',
            'categories' => [
                ['id' => 'Artists & Performers', 'label' => 'Artists & Performers'],
                ['id' => 'Epic & Mythological', 'label' => 'Epic & Mythological'],
                ['id' => 'Freedom Fighters', 'label' => 'Freedom Fighters'],
                ['id' => 'Kings & Strategists', 'label' => 'Kings & Strategists'],
                ['id' => 'Poets & Writers', 'label' => 'Poets & Writers'],
                ['id' => 'Saints & Sages', 'label' => 'Saints & Sages'],
                ['id' => 'Scientists & Thinkers', 'label' => 'Scientists & Thinkers'],
                ['id' => 'Social Reformers', 'label' => 'Social Reformers'],
                ['id' => 'Sporting Legends', 'label' => 'Sporting Legends'],
            ],
            'eraLabel' => 'Years',
            'attributionLabel' => 'Known as',
            'regionLabel' => 'Birthplace',
            'categoryLabel' => 'Field',
            'groupByCategory' => true,
        ],
        'culture' => [
            'navLabel' => 'CULTURE',
            'heroIcon' => 'Palette',
            'badgeLabel' => 'Culture of Bhārat',
            'titlePrefix' => 'Traditions',
            'titleHighlight' => 'of Bhārat',
            'subtitle' => 'Festivals and fasts, dances and drums, sarees and salwars, thalis and sweets — the traditions, attire, cuisine, arts, and crafts that colour everyday life of Bhārat.',
            'searchPlaceholder' => 'Search festivals, arts, crafts, or food...',
            'itemNoun' => 'traditions',
            'itemNounSingular' => 'tradition',
            'categories' => [
                ['id' => 'Festivals', 'label' => 'Festivals'],
                ['id' => 'Traditions & Customs', 'label' => 'Traditions & Customs'],
                ['id' => 'Classical Dance', 'label' => 'Classical Dance'],
                ['id' => 'Music & Arts', 'label' => 'Music & Arts'],
                ['id' => 'Attire', 'label' => 'Attire'],
                ['id' => 'Cuisines', 'label' => 'Cuisines'],
                ['id' => 'Crafts & Weaves', 'label' => 'Crafts & Weaves'],
                ['id' => 'Folk Art', 'label' => 'Folk Art'],
            ],
            'eraLabel' => 'Age',
            'attributionLabel' => 'Kept alive by',
            'regionLabel' => 'Origin',
            'categoryLabel' => 'Type',
            'groupByCategory' => true,
        ],
        'create' => [
            'navLabel' => 'CREATE',
            'heroIcon' => 'Sparkles',
            'badgeLabel' => 'Create — Made in Bhārat',
            'titlePrefix' => 'Creations born in',
            'titleHighlight' => 'Bhārat',
            'subtitle' => 'Zero and chess, plastic surgery and shampoo, the Moon mission and the movies — Bhārat has been inventing for 5,000 years. Explore the creations that began on this soil and changed the world.',
            'searchPlaceholder' => 'Search inventions, discoveries, or creations...',
            'itemNoun' => 'creations',
            'itemNounSingular' => 'creation',
            'categories' => [
                ['id' => 'Mathematics & Astronomy', 'label' => 'Mathematics & Astronomy'],
                ['id' => 'Medicine', 'label' => 'Medicine'],
                ['id' => 'Games & Play', 'label' => 'Games & Play'],
                ['id' => 'Everyday Inventions', 'label' => 'Everyday Inventions'],
                ['id' => 'Textiles', 'label' => 'Textiles'],
                ['id' => 'Modern Creations', 'label' => 'Modern Creations'],
            ],
            'eraLabel' => 'Era',
            'attributionLabel' => 'Pioneered by',
            'regionLabel' => 'Origin',
            'categoryLabel' => 'Domain',
            'groupByCategory' => true,
        ],
    ];

    /**
     * Convert a Tailwind-style accent ("from-[#AABBCC] to-[#DDEEFF]") to an inline gradient.
     *
     * @deprecated Use App\Support\Gradient::css() from views instead.
     */
    public static function gradient(?string $accent): string
    {
        return Gradient::css($accent);
    }

    public function browse(Request $request, string $type): View
    {
        if (! isset($this->collections[$type])) {
            abort(404);
        }

        $meta = $this->collections[$type];

        // Filtering is server-side (like the blog listing) so every filtered
        // view has its own linkable, crawlable URL.
        $search = trim((string) $request->query('q', ''));
        $category = (string) $request->query('category', '');
        $hasCategory = $category !== '' && collect($meta['categories'])->contains(fn ($c) => $c['id'] === $category);

        if (! $hasCategory) {
            $category = '';
        }

        $items = CollectionItem::ofType($type)->orderBy('name')->get();

        // Cards grouped under category headers — with a defensive bucket for
        // items whose category is missing from the metadata, so none vanish.
        $sections = $this->sections($items, $meta);

        $filtered = null;
        if ($search !== '') {
            $filtered = $items->filter(fn (CollectionItem $item) => $this->matches($item, $search))->values();
        }

        return view('collections.browse', [
            'type' => $type,
            'meta' => $meta,
            'items' => $items,
            'sections' => $sections,
            'filtered' => $filtered,
            'search' => $search,
            'activeCategory' => $category,
            'grouped' => (bool) ($meta['groupByCategory'] ?? false) && $search === '',
        ]);
    }

    /**
     * Creations browse page — /create/creations.
     *
     * The creations collection has no /{type} URL of its own (that path is the
     * maker's space), so it gets its own entry points rather than a route
     * default: injecting `type` as a default would land it in the wrong
     * position of the positional argument list.
     */
    public function browseCreate(Request $request): View
    {
        return $this->browse($request, 'create');
    }

    /**
     * One creation's page — /create/{slug}.
     */
    public function showCreate(string $slug): View
    {
        return $this->show('create', $slug);
    }

    public function show(string $type, string $slug): View
    {
        if (! isset($this->collections[$type])) {
            abort(404);
        }

        $meta = $this->collections[$type];
        $item = CollectionItem::ofType($type)->where('slug', $slug)->firstOrFail();

        // Related: same category first, then the rest — each alphabetical.
        $related = CollectionItem::ofType($type)
            ->where('slug', '!=', $slug)
            ->orderByRaw('category = ? desc', [$item->category])
            ->orderBy('name')
            ->limit(3)
            ->get();

        return view('collections.show', compact('type', 'meta', 'item', 'related'));
    }

    /**
     * Group a collection's items under its category headers.
     *
     * @param  Collection<int, CollectionItem>  $items
     * @param  array<string, mixed>  $meta
     * @return Collection<int, array{id: string, label: string, items: Collection<int, CollectionItem>}>
     */
    protected function sections(Collection $items, array $meta): Collection
    {
        $sections = collect($meta['categories'])
            ->map(fn ($cat) => [
                'id' => $cat['id'],
                'label' => $cat['label'],
                'items' => $items->where('category', $cat['id'])->values(),
            ])
            ->filter(fn ($section) => $section['items']->isNotEmpty())
            ->values();

        $listed = $sections->pluck('id');
        $orphaned = $items->reject(fn (CollectionItem $item) => $listed->contains($item->category))->values();

        if ($orphaned->isNotEmpty()) {
            $sections->push(['id' => '__other__', 'label' => 'More to Explore', 'items' => $orphaned]);
        }

        return $sections;
    }

    /**
     * Free-text match over the fields the React browse page searched.
     */
    protected function matches(CollectionItem $item, string $search): bool
    {
        $haystack = mb_strtolower(implode(' ', array_filter([
            $item->name,
            $item->native_name,
            $item->tagline,
            $item->summary,
            $item->attribution,
        ])));

        return str_contains($haystack, mb_strtolower($search));
    }

    /**
     * Metadata for a collection (used by the shared card partial).
     *
     * @return array<string, mixed>|null
     */
    public function meta(?string $type): ?array
    {
        return $type !== null ? ($this->collections[$type] ?? null) : null;
    }

    public function activities(): View
    {
        return view('collections.activities', ['activities' => CreateActivity::orderBy('title')->get()]);
    }
}
