<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Sales\Repositories\OrderRepository;

class OrderApiController extends Controller
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected ProductRepository $productRepository
    ) {}

    /**
     * Store a newly created order from the frontend checkout.
     *
     * Expects JSON body:
     * {
     *   line_items: [{ product_id, quantity, name?, price?, total?, subtotal? }],
     *   first_name, last_name, email, phone,
     *   address_1, address_2, city, state, postcode, country,
     *   customer_note?
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'line_items'          => 'required|array|min:1',
            'line_items.*.product_id' => 'required|integer|exists:products,id',
            'line_items.*.quantity'   => 'required|integer|min:1',
            'line_items.*.name'       => 'sometimes|string|max:255',
            'line_items.*.price'      => 'sometimes|numeric',
            'first_name'          => 'required|string|max:255',
            'last_name'           => 'sometimes|string|max:255',
            'email'               => 'required|email|max:255',
            'phone'               => 'required|string|max:20',
            'address_1'           => 'required|string|max:255',
            'address_2'           => 'sometimes|string|max:255',
            'city'                => 'required|string|max:255',
            'state'               => 'required|string|max:255',
            'postcode'            => 'required|string|max:20',
            'country'             => 'sometimes|string|size:2',
            'customer_note'       => 'sometimes|string|max:1000',
        ]);

        try {
            // 1. Get default channel
            $defaultChannel = DB::table('channels')->where('code', 'default')->first()
                ?: DB::table('channels')->first();

            if (! $defaultChannel) {
                return response()->json([
                    'error' => 'No channel configured. Please contact support.',
                ], 500);
            }

            // 2. Calculate totals
            $subTotal = 0;
            $items = [];

            foreach ($validated['line_items'] as $item) {
                $product = $this->productRepository->find($item['product_id']);
                if (! $product) {
                    continue;
                }

                $price = (float) ($item['price'] ?? $product->price ?? 0);
                $qty = (int) $item['quantity'];
                $total = $price * $qty;
                $subTotal += $total;

                $items[] = [
                    'product_id'   => $product->id,
                    'product_type' => \Webkul\Product\Models\Product::class,
                    'sku'          => $product->sku,
                    'type'         => $product->type,
                    'name'         => $item['name'] ?? $product->name ?? 'Product',
                    'price'      => $price,
                    'base_price' => $price,
                    'total'      => $total,
                    'base_total' => $total,
                    'total_incl_tax' => $total,
                    'base_total_incl_tax' => $total,
                    'qty_ordered' => $qty,
                    'qty_shipped' => 0,
                    'qty_invoiced' => 0,
                    'qty_canceled' => 0,
                    'qty_refunded' => 0,
                    'additional' => null,
                ];
            }

            if (empty($items)) {
                return response()->json([
                    'error' => 'No valid products found in order.',
                ], 400);
            }

            // 3. Build the order data in Bagisto format
            $baseCurrency = $defaultChannel->base_currency_code ?? 'INR';
            $channelCurrency = $defaultChannel->base_currency_code ?? 'INR';

            $orderData = [
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
                    'method'       => 'cashondelivery',
                    'method_title' => 'Cash on Delivery',
                    'additional'   => json_encode(['guest_details' => [
                        'email' => $validated['email'],
                        'phone' => $validated['phone'],
                    ]]),
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

            // 4. Create the order using Bagisto's OrderRepository (manages its own transaction)
            $order = $this->orderRepository->create($orderData);

            Log::info('Order created via API', [
                'order_id' => $order->id,
                'email' => $validated['email'],
                'total' => $subTotal,
            ]);

            return response()->json([
                'order_id' => $order->id,
                'status'   => $order->status,
                'total'    => number_format($subTotal, 2),
                'currency' => '₹',
                'message'  => 'Order placed successfully! Our team will reach out to you shortly.',
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'Failed to create order. Please try again or contact support.',
            ], 500);
        }
    }
}
