<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmazonProduct;
use Illuminate\Http\Request;

class AmazonProductController extends Controller
{
    /**
     * Display a listing of Amazon products, optionally filtered by search/category.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $category = $request->query('category');

        $products = AmazonProduct::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('asin', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when($category, fn ($query) => $query->where('category', $category))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.amazon-products.index', [
            'products' => $products,
            'categories' => $this->categories(),
            'search' => $search,
            'category' => $category,
        ]);
    }

    /**
     * Show the form for creating a new Amazon product.
     */
    public function create()
    {
        return view('admin.amazon-products.create', [
            'categories' => $this->categories(),
        ]);
    }

    /**
     * Store a newly created Amazon product.
     */
    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        AmazonProduct::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'asin' => strtoupper($validated['asin']),
            'price' => $validated['price'] ?? null,
            'rating' => $validated['rating'] ?? null,
            'ratings_count' => $validated['ratings_count'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'active' => $request->boolean('active', true),
        ]);

        session()->flash('success', "Amazon product \"{$validated['name']}\" created.");

        return redirect()->route('admin.amazon-products.index');
    }

    /**
     * Show the form for editing an Amazon product.
     */
    public function edit(int $id)
    {
        $product = AmazonProduct::findOrFail($id);

        return view('admin.amazon-products.edit', [
            'product' => $product,
            'categories' => $this->categories(),
        ]);
    }

    /**
     * Update the specified Amazon product.
     */
    public function update(Request $request, int $id)
    {
        $product = AmazonProduct::findOrFail($id);

        $validated = $this->validateProduct($request);

        $product->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'asin' => strtoupper($validated['asin']),
            'price' => $validated['price'] ?? null,
            'rating' => $validated['rating'] ?? null,
            'ratings_count' => $validated['ratings_count'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'active' => $request->boolean('active', true),
        ]);

        session()->flash('success', "Amazon product \"{$product->name}\" updated.");

        return redirect()->route('admin.amazon-products.index');
    }

    /**
     * Remove the specified Amazon product.
     */
    public function destroy(int $id)
    {
        $product = AmazonProduct::findOrFail($id);

        $product->delete();

        session()->flash('success', "Amazon product \"{$product->name}\" deleted.");

        return redirect()->route('admin.amazon-products.index');
    }

    /**
     * Validate the product fields.
     */
    protected function validateProduct(Request $request): array
    {
        return $this->validate($request, [
            'name' => 'required|max:255',
            'category' => 'required|max:255',
            'description' => 'nullable',
            'image_url' => 'nullable|url|max:500',
            'asin' => 'required|max:20',
            'price' => 'nullable|max:255',
            'rating' => 'nullable|numeric|min:0|max:5',
            'ratings_count' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
        ]);
    }

    /**
     * Distinct categories already in use (for the filter dropdown).
     */
    protected function categories(): array
    {
        return AmazonProduct::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->values()
            ->all();
    }
}
