<?php

namespace App\Livewire;

use App\Http\Controllers\ShopController;
use App\Services\ProductCatalog;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * The /shop search box and product grid.
 *
 * Replaces the full-page ?q= reload with a Livewire filter. The query is still
 * reflected in the URL (via #[Url]) so filtered views stay linkable.
 */
class ShopCatalog extends Component
{
    #[Url(as: 'q', except: '')]
    public string $search = '';

    public function clearSearch(): void
    {
        $this->search = '';
    }

    public function render()
    {
        /** @var ProductCatalog $products */
        $products = app(ProductCatalog::class);

        $all = $products->all(500);
        $search = trim($this->search);

        $filtered = $search === ''
            ? $all
            : array_values(array_filter($all, function (array $product) use ($search) {
                $haystack = mb_strtolower(implode(' ', [
                    $product['name'] ?? '',
                    $product['description'] ?? '',
                    $product['category'] ?? '',
                ]));

                return str_contains($haystack, mb_strtolower($search));
            }));

        return view('livewire.shop-catalog', [
            'products' => $filtered,
            'search' => $search,
            'categories' => $this->categories($all),
        ]);
    }

    /**
     * Storefront categories that actually have products, in ShopController order.
     *
     * @param  array<int, array<string, mixed>>  $products
     * @return array<int, string>
     */
    protected function categories(array $products): array
    {
        $present = array_unique(array_column($products, 'category'));

        $categories = array_values(array_filter(
            array_keys(ShopController::CATEGORIES),
            fn (string $name) => in_array($name, $present, true)
        ));

        foreach ($present as $name) {
            if (! in_array($name, $categories, true)) {
                $categories[] = $name;
            }
        }

        return $categories;
    }
}
