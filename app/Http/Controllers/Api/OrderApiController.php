<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CheckoutOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Webkul\Sales\Repositories\OrderRepository;

class OrderApiController extends Controller
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected CheckoutOrderService $checkoutOrder,
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
        $validated = $this->checkoutOrder->validateCheckout($request);

        try {
            [$items, $subTotal] = $this->checkoutOrder->buildItems($validated['line_items']);

            if (empty($items)) {
                return response()->json([
                    'error' => 'No valid products found in order.',
                ], 400);
            }

            $orderData = $this->checkoutOrder->orderData(
                $validated,
                $items,
                $subTotal,
                'cashondelivery',
                'Cash on Delivery',
                ['guest_details' => [
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                ]],
            );

            // Create the order using Bagisto's OrderRepository (manages its own transaction)
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
