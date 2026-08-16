<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AmazonProduct;

class AmazonProductApiController extends Controller
{
    /**
     * Return all active Amazon affiliate products, grouped by category.
     */
    public function index()
    {
        $products = AmazonProduct::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (AmazonProduct $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'description' => $product->description,
                'image_url' => $product->image_url,
                'price' => $product->price,
                'rating' => $product->rating !== null ? (float) $product->rating : null,
                'ratings_count' => $product->ratings_count,
                'link' => $product->link,
            ]);

        $grouped = $products->groupBy('category')
            ->map(fn ($items, $category) => [
                'category' => $category,
                'products' => $items->values(),
            ])
            ->values();

        return response()->json([
            'success' => true,
            'data' => $grouped,
        ]);
    }
}
