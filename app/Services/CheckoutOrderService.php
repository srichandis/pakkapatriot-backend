<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Webkul\Product\Repositories\ProductRepository;

/**
 * Shared logic for the public checkout APIs.
 *
 * Both the plain order endpoint (cash on delivery) and the Razorpay
 * payment flow build Bagisto orders from the same guest checkout payload,
 * so the validation and order-data construction live here in one place.
 */
class CheckoutOrderService
{
    public function __construct(protected ProductRepository $productRepository) {}

    /**
     * Validate the shared guest-checkout JSON body.
     *
     * @return array{line_items: array, first_name: string, last_name?: string, email: string, phone: string, address_1: string, address_2?: string, city: string, state: string, postcode: string, country?: string, customer_note?: string}
     */
    public function validateCheckout(Request $request): array
    {
        return $request->validate([
            'line_items'              => 'required|array|min:1',
            'line_items.*.product_id' => 'required|integer|exists:products,id',
            'line_items.*.quantity'   => 'required|integer|min:1',
            'line_items.*.name'       => 'sometimes|string|max:255',
            'line_items.*.price'      => 'sometimes|numeric',
            'first_name'              => 'required|string|max:255',
            'last_name'               => 'sometimes|string|max:255',
            'email'                   => 'required|email|max:255',
            'phone'                   => 'required|string|max:20',
            'address_1'               => 'required|string|max:255',
            'address_2'               => 'sometimes|string|max:255',
            'city'                    => 'required|string|max:255',
            'state'                   => 'required|string|max:255',
            'postcode'                => 'required|string|max:20',
            'country'                 => 'sometimes|string|size:2',
            'customer_note'           => 'sometimes|string|max:1000',
        ]);
    }

    /**
     * Resolve the line items against the catalog and return the Bagisto
     * item rows plus the computed subtotal.
     *
     * @param  array<int, array{product_id: int, quantity: int, price?: mixed, name?: string}>  $lineItems
     * @return array{0: array<int, array<string, mixed>>, 1: float}
     */
    public function buildItems(array $lineItems): array
    {
        $subTotal = 0;
        $items = [];

        foreach ($lineItems as $item) {
            $product = $this->productRepository->find($item['product_id']);

            if (! $product) {
                continue;
            }

            $price = (float) ($item['price'] ?? $product->price ?? 0);
            $qty = (int) $item['quantity'];
            $total = $price * $qty;
            $subTotal += $total;

            $items[] = [
                'product_id'          => $product->id,
                'product_type'        => \Webkul\Product\Models\Product::class,
                'sku'                 => $product->sku,
                'type'                => $product->type,
                'name'                => $item['name'] ?? $product->name ?? 'Product',
                'price'               => $price,
                'base_price'          => $price,
                'total'               => $total,
                'base_total'          => $total,
                'total_incl_tax'      => $total,
                'base_total_incl_tax' => $total,
                'qty_ordered'         => $qty,
                'qty_shipped'         => 0,
                'qty_invoiced'        => 0,
                'qty_canceled'        => 0,
                'qty_refunded'        => 0,
                'additional'          => null,
            ];
        }

        return [$items, $subTotal];
    }

    /**
     * Build the full Bagisto order payload for a guest checkout.
     *
     * @param  array<string, mixed>  $validated
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<string, mixed>  $paymentAdditional
     * @return array<string, mixed>
     *
     * @throws \RuntimeException When no channel is configured.
     */
    public function orderData(
        array $validated,
        array $items,
        float $subTotal,
        string $paymentMethod,
        string $paymentTitle,
        array $paymentAdditional = [],
    ): array {
        $defaultChannel = DB::table('channels')->where('code', 'default')->first()
            ?: DB::table('channels')->first();

        if (! $defaultChannel) {
            throw new \RuntimeException('No channel configured.');
        }

        $baseCurrency = $defaultChannel->base_currency_code ?? 'INR';
        $channelCurrency = $defaultChannel->base_currency_code ?? 'INR';

        return [
            'cart_id'                => null,
            'customer_email'         => $validated['email'],
            'customer_first_name'    => $validated['first_name'] ?? '',
            'customer_last_name'     => $validated['last_name'] ?? '',
            'customer_type'          => null,
            'customer_id'            => null,
            'channel_id'             => $defaultChannel->id,
            'channel_type'           => \Webkul\Core\Models\Channel::class,
            'channel_name'           => $defaultChannel->name ?? 'Default',
            'order_currency_code'    => $channelCurrency,
            'base_currency_code'     => $baseCurrency,
            'grand_total'            => $subTotal,
            'base_grand_total'       => $subTotal,
            'sub_total'              => $subTotal,
            'base_sub_total'         => $subTotal,
            'tax_amount'             => 0,
            'base_tax_amount'        => 0,
            'discount_amount'        => 0,
            'base_discount_amount'   => 0,
            'shipping_method'        => 'free_free',
            'shipping_title'         => 'Free Shipping',
            'shipping_amount'        => 0,
            'base_shipping_amount'   => 0,
            'coupon_code'            => null,
            'customer_note'          => $validated['customer_note'] ?? null,
            'status'                 => 'pending',
            'is_guest'               => 1,
            'converted_at'           => now(),
            'created_at'             => now(),
            'updated_at'             => now(),
            'items'                  => $items,
            'payment'                => [
                'method'       => $paymentMethod,
                'method_title' => $paymentTitle,
                'additional'   => json_encode($paymentAdditional),
            ],
            'billing_address' => [
                'address_type' => 'billing',
                'first_name'   => $validated['first_name'] ?? '',
                'last_name'    => $validated['last_name'] ?? '',
                'email'        => $validated['email'],
                'phone'        => $validated['phone'],
                'address1'     => $validated['address_1'],
                'address2'     => $validated['address_2'] ?? '',
                'city'         => $validated['city'],
                'state'        => $validated['state'],
                'postcode'     => $validated['postcode'],
                'country'      => $validated['country'] ?? 'IN',
            ],
            'shipping_address' => [
                'address_type' => 'shipping',
                'first_name'   => $validated['first_name'] ?? '',
                'last_name'    => $validated['last_name'] ?? '',
                'email'        => $validated['email'],
                'phone'        => $validated['phone'],
                'address1'     => $validated['address_1'],
                'address2'     => $validated['address_2'] ?? '',
                'city'         => $validated['city'],
                'state'        => $validated['state'],
                'postcode'     => $validated['postcode'],
                'country'      => $validated['country'] ?? 'IN',
            ],
        ];
    }
}
