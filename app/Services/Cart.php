<?php

namespace App\Services;

use Illuminate\Contracts\Session\Session;

/**
 * The storefront cart.
 *
 * Replaces the React app's CartContext (and the localStorage shim that followed
 * it — `pakka_cart_items` in resources/js/app.js) with server-owned state that
 * survives a page reload, works without JavaScript-enabled localStorage, and is
 * the single source of truth for both the floating badge and the drawer.
 *
 * Only product ids and quantities are stored in the session; product details are
 * resolved from the catalogue on read so prices can never go stale.
 */
class Cart
{
    public const SESSION_KEY = 'pakka_cart';

    public function __construct(
        protected Session $session,
        protected ProductCatalog $products,
    ) {}

    /**
     * Cart lines, resolved against the live catalogue.
     *
     * Products that have since been unpublished are dropped from the cart.
     *
     * @return array<int, array{product: array<string, mixed>, quantity: int, line_total: int}>
     */
    public function items(): array
    {
        $lines = [];

        foreach ($this->quantities() as $productId => $quantity) {
            $product = $this->products->find((int) $productId);

            if ($product === null) {
                continue;
            }

            $lines[] = [
                'product' => $product,
                'quantity' => $quantity,
                'line_total' => $this->priceOf($product) * $quantity,
            ];
        }

        return $lines;
    }

    /**
     * Add a quantity of a product, or increase it when already in the cart.
     */
    public function add(int $productId, int $quantity = 1): void
    {
        if ($quantity < 1) {
            return;
        }

        $quantities = $this->quantities();
        $quantities[$productId] = ($quantities[$productId] ?? 0) + $quantity;

        $this->save($quantities);
    }

    /**
     * Set an exact quantity; anything at or below zero removes the line.
     */
    public function setQuantity(int $productId, int $quantity): void
    {
        $quantities = $this->quantities();

        if ($quantity <= 0) {
            unset($quantities[$productId]);
        } else {
            $quantities[$productId] = min($quantity, 99);
        }

        $this->save($quantities);
    }

    public function remove(int $productId): void
    {
        $quantities = $this->quantities();
        unset($quantities[$productId]);

        $this->save($quantities);
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }

    public function totalItems(): int
    {
        return array_sum($this->quantities());
    }

    /**
     * Subtotal in whole rupees, matching the front-end's display.
     */
    public function totalPrice(): int
    {
        return array_sum(array_column($this->items(), 'line_total'));
    }

    public function isEmpty(): bool
    {
        return $this->quantities() === [];
    }

    /**
     * The stored [productId => quantity] map.
     *
     * @return array<int, int>
     */
    protected function quantities(): array
    {
        $stored = $this->session->get(self::SESSION_KEY, []);

        if (! is_array($stored)) {
            return [];
        }

        $clean = [];

        foreach ($stored as $productId => $quantity) {
            $quantity = (int) $quantity;

            if ($quantity > 0) {
                $clean[(int) $productId] = $quantity;
            }
        }

        return $clean;
    }

    /**
     * @param  array<int, int>  $quantities
     */
    protected function save(array $quantities): void
    {
        if ($quantities === []) {
            $this->session->forget(self::SESSION_KEY);

            return;
        }

        $this->session->put(self::SESSION_KEY, $quantities);
    }

    /**
     * Price of a catalogue product as an integer (stored as "1,999" or "350").
     *
     * @param  array<string, mixed>  $product
     */
    protected function priceOf(array $product): int
    {
        return (int) preg_replace('/[^0-9]/', '', (string) ($product['price'] ?? 0));
    }
}
