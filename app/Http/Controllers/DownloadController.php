<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * DOWNLOADS — the printable library.
 *
 * The eleven material types live in config/downloads.php; every entry there
 * gets a page at /downloads/{category}. An item whose file has not been
 * uploaded yet is flagged `available => false` so the page can show a
 * "coming soon" state instead of a link that 404s.
 */
class DownloadController extends Controller
{
    /**
     * The library hub: every material type with its item count.
     */
    public function index(): View
    {
        $categories = collect($this->categories())
            ->map(fn (array $category, string $slug) => $category + [
                'slug' => $slug,
                'count' => count($category['items']),
            ])
            ->values();

        return view('downloads.index', [
            'categories' => $categories,
            'total' => (int) $categories->sum('count'),
        ]);
    }

    /**
     * One material type and everything it holds.
     */
    public function show(string $category): View
    {
        $categories = $this->categories();

        abort_unless(isset($categories[$category]), 404);

        $meta = $categories[$category];

        return view('downloads.category', [
            'slug' => $category,
            'meta' => $meta,
            'items' => $this->items($category, $meta['items']),
            'siblings' => collect($categories)
                ->map(fn (array $item, string $slug) => ['slug' => $slug, 'title' => $item['title'], 'emoji' => $item['emoji']])
                ->reject(fn (array $item) => $item['slug'] === $category)
                ->values()
                ->all(),
        ]);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function categories(): array
    {
        return config('downloads.categories', []);
    }

    /**
     * Resolve where each item's file is expected to live, and whether it is
     * there yet.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    protected function items(string $category, array $items): array
    {
        return array_map(function (array $item) use ($category) {
            $file = 'downloads/'.$category.'/'.Str::slug($item['title']).'.'.$item['format'];

            return $item + [
                'file' => $file,
                'url' => asset($file),
                'available' => file_exists(public_path($file)),
            ];
        }, $items);
    }
}
