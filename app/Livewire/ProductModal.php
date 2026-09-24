<?php

namespace App\Livewire;

use App\Services\Cart;
use App\Services\ProductCatalog;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * The product detail modal.
 *
 * Opened by dispatching `open-product` with a product id from any product card
 * (see resources/views/components/pp-product-card.blade.php). The product is
 * re-read from the catalogue server-side, so the page never ships a serialised
 * copy of every product just to open one modal.
 */
class ProductModal extends Component
{
    public ?array $product = null;

    public int $quantity = 1;

    public int $imageIndex = 0;

    public bool $justAdded = false;

    /**
     * Colour swatch palette, ported from COLOUR_HEX in resources/js/app.js.
     */
    protected const COLOUR_HEX = [
        'white' => '#F7F5EF',
        'sand' => '#E5D6BB',
        'terracotta' => '#C1683F',
        'sage green' => '#8A9A7B',
        'olive' => '#6B7343',
        'navy blue' => '#2B3A67',
        'charcoal' => '#33312E',
    ];

    #[On('open-product')]
    public function open(int $id): void
    {
        $product = app(ProductCatalog::class)->find($id);

        if ($product === null) {
            return;
        }

        $this->product = $product;
        $this->quantity = 1;
        $this->imageIndex = 0;
        $this->justAdded = false;
    }

    /**
     * Escape closes the modal (dispatched from resources/js/app.js).
     */
    #[On('close-product')]
    public function close(): void
    {
        $this->product = null;
        $this->justAdded = false;
    }

    public function increment(): void
    {
        $this->quantity = min(99, $this->quantity + 1);
    }

    public function decrement(): void
    {
        $this->quantity = max(1, $this->quantity - 1);
    }

    public function selectImage(int $index): void
    {
        $images = $this->images();

        if ($index >= 0 && $index < count($images)) {
            $this->imageIndex = $index;
        }
    }

    /**
     * Add the product to the cart. `$openDrawer` mirrors the React modal's
     * "BUY NOW" (open the bag) versus "ADD TO BAG" (inline confirmation).
     */
    public function addToCart(bool $openDrawer = false): void
    {
        if ($this->product === null) {
            return;
        }

        app(Cart::class)->add((int) $this->product['id'], $this->quantity);
        $this->dispatch('cart-updated');

        if ($openDrawer) {
            $this->close();
            $this->dispatch('open-cart');

            return;
        }

        $this->justAdded = true;
    }

    /**
     * Every image for the product, falling back to its main image.
     *
     * @return array<int, string>
     */
    public function images(): array
    {
        if ($this->product === null) {
            return [];
        }

        $images = array_values(array_filter($this->product['images'] ?? []));

        return $images !== [] ? $images : [(string) $this->product['image_url']];
    }

    public function activeImage(): string
    {
        $images = $this->images();

        return $images[min($this->imageIndex, count($images) - 1)] ?? '';
    }

    /**
     * Human label for a colour variant, read off its image filename.
     * Ported from colourLabelFromPath() in resources/js/app.js.
     */
    public function colourLabel(string $path): string
    {
        $base = pathinfo($path, PATHINFO_FILENAME);
        $name = strtolower($base);

        foreach ([
            'navy_blue' => 'Navy Blue',
            'navy' => 'Navy Blue',
            'sage_green' => 'Sage Green',
            'terracotta' => 'Terracotta',
            'charcoal' => 'Charcoal',
            'olive' => 'Olive',
            'sand' => 'Sand',
            'white' => 'White',
        ] as $token => $label) {
            if (str_contains($name, $token)) {
                return $label;
            }
        }

        $last = str_contains($name, '_') ? substr($name, strrpos($name, '_') + 1) : $name;

        return ucfirst($last);
    }

    public function colourHex(string $label): string
    {
        return self::COLOUR_HEX[strtolower($label)] ?? '#C8C5B9';
    }

    public function render()
    {
        return view('livewire.product-modal', [
            'images' => $this->images(),
            'active' => $this->activeImage(),
        ]);
    }
}
