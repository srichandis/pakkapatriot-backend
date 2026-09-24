<?php

namespace App\Livewire;

use App\Services\Cart;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * The floating cart badge and the bag drawer.
 *
 * Both are rendered by this one component so the line items, the counts and the
 * subtotal all come from a single server-side cart (see App\Services\Cart).
 */
class CartDrawer extends Component
{
    public bool $open = false;

    /**
     * Any other component that changes the cart re-renders this one.
     */
    #[On('cart-updated')]
    public function refresh(): void {}

    /**
     * "BUY NOW" in the product modal goes straight to the bag.
     */
    #[On('open-cart')]
    public function showDrawer(): void
    {
        $this->open = true;
    }

    /**
     * Escape closes the drawer (dispatched from resources/js/app.js).
     */
    #[On('close-cart')]
    public function handleCloseShortcut(): void
    {
        $this->open = false;
    }

    public function openDrawer(): void
    {
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function increment(int $productId): void
    {
        $this->cart()->add($productId);
        $this->broadcast();
    }

    public function decrement(int $productId): void
    {
        $line = collect($this->cart()->items())->firstWhere('product.id', $productId);

        if ($line === null) {
            return;
        }

        $this->cart()->setQuantity($productId, $line['quantity'] - 1);
        $this->broadcast();
    }

    public function remove(int $productId): void
    {
        $this->cart()->remove($productId);
        $this->broadcast();
    }

    public function clear(): void
    {
        $this->cart()->clear();
        $this->broadcast();
    }

    public function render()
    {
        $cart = $this->cart();

        return view('livewire.cart-drawer', [
            'items' => $cart->items(),
            'totalItems' => $cart->totalItems(),
            'totalPrice' => $cart->totalPrice(),
        ]);
    }

    protected function broadcast(): void
    {
        $this->dispatch('cart-updated');
    }

    protected function cart(): Cart
    {
        return app(Cart::class);
    }
}
