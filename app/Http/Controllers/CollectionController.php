<?php

namespace App\Http\Controllers;

use App\Models\CollectionItem;
use App\Models\CreateActivity;
use App\Models\EBook;
use App\Models\Game;
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
     */
    public static function gradient(?string $accent): string
    {
        if ($accent && preg_match_all('/#([0-9a-fA-F]{3,8})/', $accent, $m) && count($m[1]) >= 2) {
            return 'linear-gradient(135deg, #' . $m[1][0] . ', #' . $m[1][1] . ')';
        }

        return 'linear-gradient(135deg, #0A2240, #1A3A5C)';
    }

    public function browse(string $type): View
    {
        if (! isset($this->collections[$type])) {
            abort(404);
        }

        $meta = $this->collections[$type];
        $items = CollectionItem::ofType($type)->get();

        return view('collections.browse', compact('type', 'meta', 'items'));
    }

    public function show(string $type, string $slug): View
    {
        if (! isset($this->collections[$type])) {
            abort(404);
        }

        $meta = $this->collections[$type];
        $item = CollectionItem::ofType($type)->where('slug', $slug)->firstOrFail();
        $related = CollectionItem::ofType($type)->where('slug', '!=', $slug)->inRandomOrder()->limit(3)->get();

        return view('collections.show', compact('type', 'meta', 'item', 'related'));
    }

    public function games(): View
    {
        return view('collections.games', ['games' => Game::orderBy('title')->get()]);
    }

    public function ebooks(): View
    {
        return view('collections.ebooks', ['ebooks' => EBook::orderBy('title')->get()]);
    }

    public function activities(): View
    {
        return view('collections.activities', ['activities' => CreateActivity::orderBy('title')->get()]);
    }
}
