<?php

namespace App\Http\Controllers;

use App\Services\ProductCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * The merchandise store — /shop and /shop/{category}.
 *
 * Ported from the React MadeInIndiaPage / MadeInBharatCategoryPage. Browsing is
 * server-side (?q= on the index) so every view has its own URL.
 */
class ShopController extends Controller
{
    /**
     * Storefront category order, with the blurb shown on each category page.
     */
    public const CATEGORIES = [
        'T-Shirts' => 'Everyday classics carrying the icons of Bhārat — soft cotton tees in six signature designs.',
        'Mugs' => 'Start every morning with Bhārat. Ceramic mugs printed with the monuments and heroes we love.',
        'Posters' => 'Frameworthy prints of the monuments, heroes, and symbols of Bhārat.',
        'Stickers' => 'Slap a little Bhārat on your laptop and water bottle. Die-cut stickers in every design.',
        'Notebooks' => 'Journal, sketch, and plan alongside the icons of Bhārat.',
        'Caps' => 'Top off the look with the designs — printed caps for every patriot.',
        'Photo Frames' => 'Frame the icons of Bhārat — premium frames carrying the designs on your wall or desk.',
    ];

    public function __construct(protected ProductCatalog $products) {}

    /**
     * Category name → URL slug ("Photo Frames" → "photo-frames").
     */
    public static function categorySlug(string $category): string
    {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $category) ?? '');

        return trim($slug, '-');
    }

    public function index(Request $request): View
    {
        $all = $this->products->all(500);
        $search = trim((string) $request->query('q', ''));

        $products = $search === ''
            ? $all
            : array_values(array_filter($all, fn (array $product) => $this->matches($product, $search)));

        return view('shop.index', [
            'products' => $products,
            'all' => $all,
            'categories' => $this->categories($all),
            'search' => $search,
        ]);
    }

    public function category(string $categorySlug): View|Response
    {
        $all = $this->products->all(500);
        $categories = $this->categories($all);

        // Match the React page's lookup: by slug, against the categories that
        // actually have products.
        $name = null;
        foreach ($categories as $candidate) {
            if (self::categorySlug($candidate) === $categorySlug) {
                $name = $candidate;
                break;
            }
        }

        if ($name === null) {
            // React rendered "Category not found"; answer 404 with the same page.
            return response()->view('shop.category', [
                'name' => null,
                'products' => [],
                'categories' => $categories,
            ], 404);
        }

        return view('shop.category', [
            'name' => $name,
            'blurb' => self::CATEGORIES[$name] ?? 'Every design, printed on '.mb_strtolower($name).'.',
            'products' => array_values(array_filter($all, fn (array $product) => $product['category'] === $name)),
            'categories' => $categories,
        ]);
    }

    /**
     * Storefront categories that actually have products, in CATEGORIES order,
     * with any unknown category appended (as the React page did).
     *
     * @param  array<int, array<string, mixed>>  $products
     * @return array<int, string>
     */
    protected function categories(array $products): array
    {
        $present = array_unique(array_column($products, 'category'));
        $categories = array_values(array_filter(
            array_keys(self::CATEGORIES),
            fn (string $name) => in_array($name, $present, true)
        ));

        foreach ($present as $name) {
            if (! in_array($name, $categories, true)) {
                $categories[] = $name;
            }
        }

        return $categories;
    }

    /**
     * Free-text match over the fields the React storefront searched.
     *
     * @param  array<string, mixed>  $product
     */
    protected function matches(array $product, string $search): bool
    {
        $haystack = mb_strtolower(implode(' ', [
            $product['name'],
            $product['description'],
            $product['category'],
        ]));

        return str_contains($haystack, mb_strtolower($search));
    }
}
