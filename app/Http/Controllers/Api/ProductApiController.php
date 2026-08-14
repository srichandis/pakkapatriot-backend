<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductApiController extends Controller
{
    /**
     * Default channel/locale used when core() helper isn't available.
     */
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
     * Return paginated products as JSON.
     */
    public function index(): JsonResponse
    {
        $perPage = min((int) request()->query('per_page', 50), 100);
        $search = request()->query('search');

        try {
            $query = DB::table('product_flat')
                ->where('status', 1)
                ->where('visible_individually', 1)
                ->where('channel', $this->defaultChannel)
                ->where('locale', $this->defaultLocale);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('product_flat.name', 'like', '%' . $search . '%')
                      ->orWhere('product_flat.description', 'like', '%' . $search . '%');
                });
            }

            $products = $query->orderBy('product_flat.created_at', 'desc')
                ->paginate($perPage);

            $data = collect($products->items())->map(function ($product) {
                return $this->formatProduct($product);
            });

            return response()->json([
                'data' => $data,
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'data' => [],
                'error' => $e->getMessage(),
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage,
                    'total' => 0,
                ],
            ]);
        }
    }

    /**
     * Return a single product by ID.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = DB::table('product_flat')
                ->where('product_id', $id)
                ->where('channel', $this->defaultChannel)
                ->where('locale', $this->defaultLocale)
                ->firstOrFail();

            return response()->json([
                'data' => $this->formatProduct($product),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Format a product_flat row into the shape the frontend expects.
     */
    protected function formatProduct($product): array
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
            'image_url' => $this->getProductImageUrl($product->product_id),
            'images' => $this->getProductImages($product->product_id),
            'category' => $this->getProductCategory($product->product_id),
            'in_stock' => true,
            'sku' => $product->sku ?? '',
            'slug' => $product->url_key ?? '',
        ];
    }

    /**
     * Get the first product image URL (relative path for Vite proxy).
     */
    protected function getProductImageUrl(int $productId): string
    {
        try {
            $image = DB::table('product_images')
                ->where('product_id', $productId)
                ->orderBy('position')
                ->first();

            if ($image && $image->path) {
                // Return relative path so it goes through the Vite /storage proxy
                return '/storage/' . $image->path;
            }
        } catch (\Throwable $e) {
            // Fall through to default image
        }

        return 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?q=80&w=600&auto=format&fit=crop';
    }

    /**
     * Get every product image URL (colour variants), ordered by position.
     */
    protected function getProductImages(int $productId): array
    {
        try {
            $images = DB::table('product_images')
                ->where('product_id', $productId)
                ->orderBy('position')
                ->pluck('path');

            return $images
                ->map(fn ($path) => '/storage/' . $path)
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get the product category name.
     */
    protected function getProductCategory(int $productId): string
    {
        try {
            $category = DB::table('product_categories')
                ->join('category_translations', 'product_categories.category_id', '=', 'category_translations.category_id')
                ->where('product_categories.product_id', $productId)
                ->where('category_translations.locale', app()->getLocale())
                ->select('category_translations.name')
                ->first();

            return $category?->name ?? 'General';
        } catch (\Throwable $e) {
            return 'General';
        }
    }
}
