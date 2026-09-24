<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Reads the Bagisto product catalogue and normalises it into the flat shape the
 * Pakka Patriot front-end (Blade views + JSON API) expects.
 */
class ProductCatalog
{
    protected string $defaultChannel;

    protected string $defaultLocale;

    public function __construct()
    {
        try {
            $this->defaultChannel = core()->getDefaultChannelCode() ?? 'default';
            $this->defaultLocale = core()->getDefaultLocaleCodeFromDefaultChannel() ?? app()->getLocale() ?? 'en';
        } catch (\Throwable $e) {
            $this->defaultChannel = DB::table('channels')->where('code', 'default')->value('code') ?? 'default';
            $this->defaultLocale = app()->getLocale() ?? 'en';
        }
    }

    /**
     * Paginated products, newest first.
     */
    public function paginate(int $perPage = 50, ?string $search = null): LengthAwarePaginator
    {
        $query = $this->query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('product_flat.name', 'like', '%'.$search.'%')
                    ->orWhere('product_flat.description', 'like', '%'.$search.'%');
            });
        }

        return $query->orderBy('product_flat.created_at', 'desc')->paginate($perPage);
    }

    /**
     * Every visible product, formatted for the front-end.
     *
     * @return array<int, array<string, mixed>>
     */
    public function all(int $limit = 50, ?string $search = null): array
    {
        $query = $this->query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('product_flat.name', 'like', '%'.$search.'%')
                    ->orWhere('product_flat.description', 'like', '%'.$search.'%');
            });
        }

        return $query->orderBy('product_flat.created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn ($product) => $this->format($product))
            ->all();
    }

    /**
     * A single product by id, or null when it is missing.
     *
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array
    {
        $product = $this->query()->where('product_flat.product_id', $id)->first();

        return $product ? $this->format($product) : null;
    }

    /**
     * Base query for visible products in the default channel/locale.
     */
    protected function query()
    {
        return DB::table('product_flat')
            ->where('status', 1)
            ->where('visible_individually', 1)
            ->where('channel', $this->defaultChannel)
            ->where('locale', $this->defaultLocale);
    }

    /**
     * Format a product_flat row into the shape the front-end expects.
     *
     * @return array<string, mixed>
     */
    public function format(object $product): array
    {
        $price = $product->price ?? 0;
        $specialPrice = $product->special_price ?? null;
        $onSale = ! is_null($specialPrice) && (float) $specialPrice > 0;

        return [
            'id' => (int) $product->product_id,
            'name' => $product->name ?? '',
            'description' => strip_tags($product->description ?? ''),
            'short_description' => strip_tags($product->short_description ?? ''),
            'price' => number_format((float) $price, 0, '.', ''),
            'regular_price' => number_format((float) ($specialPrice ?: $price), 0, '.', ''),
            'sale_price' => $onSale ? number_format((float) $specialPrice, 0, '.', '') : null,
            'on_sale' => $onSale,
            'image_url' => $this->imageUrl((int) $product->product_id),
            'images' => $this->images((int) $product->product_id),
            'category' => $this->category((int) $product->product_id),
            'in_stock' => true,
            'sku' => $product->sku ?? '',
            'slug' => $product->url_key ?? '',
        ];
    }

    /**
     * Products matching a free-text search term (used by site search).
     */
    public function search(string $term, int $limit = 20): array
    {
        return $this->all($limit, $term);
    }

    /**
     * Absolute URL of the first product image.
     */
    protected function imageUrl(int $productId): string
    {
        $images = $this->images($productId);

        return $images[0] ?? 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?q=80&w=600&auto=format&fit=crop';
    }

    /**
     * Every product image URL (colour variants), ordered by position.
     *
     * @return array<int, string>
     */
    protected function images(int $productId): array
    {
        try {
            return DB::table('product_images')
                ->where('product_id', $productId)
                ->orderBy('position')
                ->pluck('path')
                ->filter()
                ->map(fn ($path) => url('/storage/'.$path))
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Product category name for the given locale.
     */
    protected function category(int $productId): string
    {
        try {
            $category = DB::table('product_categories')
                ->join('category_translations', 'product_categories.category_id', '=', 'category_translations.category_id')
                ->where('product_categories.product_id', $productId)
                ->where('category_translations.locale', $this->defaultLocale)
                ->select('category_translations.name')
                ->first();

            return $category?->name ?? 'General';
        } catch (\Throwable $e) {
            return 'General';
        }
    }

    /**
     * Map a raw paginator page of product_flat rows.
     *
     * @param  Collection<int, object>  $products
     * @return array<int, array<string, mixed>>
     */
    public function formatMany(Collection $products): array
    {
        return $products->map(fn ($product) => $this->format($product))->all();
    }
}
