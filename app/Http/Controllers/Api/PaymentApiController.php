<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CheckoutOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Utility;
use Webkul\Razorpay\Payment\RazorpayPayment;
use Webkul\Sales\Models\Invoice;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\OrderTransactionRepository;

/**
 * Razorpay payment flow for the mobile/web apps.
 *
 * The app calls `init` to create a Razorpay Payment Link for the cart,
 * then opens the returned `short_url`. Payment is confirmed either by the
 * Razorpay redirect (`callback`), the webhook (`webhook`), or by the app
 * polling `status`. The Bagisto order is created exactly once per link.
 */
class PaymentApiController extends Controller
{
    /**
     * How long a pending checkout is remembered while the customer pays.
     */
    protected const PENDING_TTL_SECONDS = 7200;

    public function __construct(
        protected RazorpayPayment $razorpayPayment,
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository,
        protected OrderTransactionRepository $orderTransactionRepository,
        protected CheckoutOrderService $checkoutOrder,
    ) {}

    /**
     * Create a Razorpay Payment Link for the cart and remember the checkout
     * so the order can be created once the link is paid.
     */
    public function init(Request $request): JsonResponse
    {
        $validated = $this->checkoutOrder->validateCheckout($request);

        if (! $this->hasCredentials()) {
            return response()->json([
                'error' => 'Online payments are not configured yet. Please contact support.',
            ], 503);
        }

        try {
            [$items, $subTotal] = $this->checkoutOrder->buildItems($validated['line_items']);

            if (empty($items)) {
                return response()->json([
                    'error' => 'No valid products found in order.',
                ], 400);
            }

            $customerName = trim(($validated['first_name'] ?? '').' '.($validated['last_name'] ?? '')) ?: 'Customer';

            $link = $this->api()->paymentLink->create([
                'amount'          => (int) round($subTotal * 100),
                'currency'        => 'INR',
                'accept_partial'  => false,
                'description'     => 'Pakka Patriot order — '.$customerName,
                'customer'        => [
                    'name'    => $customerName,
                    'email'   => $validated['email'],
                    'contact' => $validated['phone'],
                ],
                'notify'          => ['email' => true, 'sms' => true],
                'callback_url'    => $this->callbackUrl(),
                'callback_method' => 'get',
                'expire_by'       => now()->addMinutes(45)->getTimestamp(),
                'notes'           => ['source' => 'flutter-app'],
            ]);

            Cache::put(
                $this->pendingKey($link->id),
                array_merge($validated, ['currency' => 'INR', 'amount' => $subTotal]),
                now()->addSeconds(self::PENDING_TTL_SECONDS),
            );

            Log::info('Razorpay payment link created', [
                'payment_link_id' => $link->id,
                'email'           => $validated['email'],
                'amount'          => $subTotal,
            ]);

            return response()->json([
                'payment_link_id' => $link->id,
                'short_url'       => $link->short_url,
                'amount'          => $subTotal,
                'currency'        => 'INR',
                'message'         => 'Payment link created.',
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Razorpay init failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Could not start the payment. Please try again or contact support.',
            ], 500);
        }
    }

    /**
     * Browser redirect target after the customer pays on Razorpay's hosted
     * page. Verifies the payment-link signature and places the order.
     */
    public function callback(Request $request): \Illuminate\Http\Response
    {
        $attributes = $request->only([
            'razorpay_payment_link_id',
            'razorpay_payment_link_reference_id',
            'razorpay_payment_link_status',
            'razorpay_payment_id',
            'razorpay_signature',
        ]);

        $linkId = $attributes['razorpay_payment_link_id'] ?? null;
        $status = $attributes['razorpay_payment_link_status'] ?? null;

        $paid = false;

        if ($linkId && $status === 'paid') {
            try {
                (new Utility())->verifyPaymentSignature(array_filter($attributes));

                $this->rememberPaymentId($linkId, $attributes['razorpay_payment_id'] ?? null);

                $this->createOrderFromPendingLink($linkId);

                $paid = true;
            } catch (\Throwable $e) {
                Log::warning('Razorpay callback signature verification failed', [
                    'link_id' => $linkId,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        $html = $paid
            ? '<html><body style="font-family: sans-serif; text-align: center; padding: 48px;"><h2>Payment successful ✅</h2><p>Your Pakka Patriot order has been placed. You can close this tab and return to the app.</p></body></html>'
            : '<html><body style="font-family: sans-serif; text-align: center; padding: 48px;"><h2>Payment not completed</h2><p>Your order was not placed. Please close this tab and try again in the app.</p></body></html>';

        return response($html, 200, ['Content-Type' => 'text/html']);
    }

    /**
     * Razorpay webhook. Confirms payment_link.paid events and creates the
     * order idempotently (safety net when the app never calls back).
     */
    public function webhook(Request $request): JsonResponse
    {
        $signature = $request->header('X-Razorpay-Signature');
        $payload = $request->getContent();
        $secret = $this->webhookSecret();

        if (! $signature || ! $secret) {
            return response()->json(['error' => 'Webhook not configured.'], 400);
        }

        try {
            (new Utility())->verifyWebhookSignature($payload, $signature, $secret);
        } catch (\Throwable $e) {
            Log::warning('Razorpay webhook signature invalid', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Invalid signature.'], 401);
        }

        $data = json_decode($payload, true);
        $event = $data['event'] ?? '';
        $entity = $data['payload'] ?? [];

        if ($event === 'payment_link.paid') {
            $linkId = $entity['payment_link']['entity']['id'] ?? null;
            $paymentId = $entity['payment']['entity']['id'] ?? null;

            if ($linkId) {
                $this->rememberPaymentId($linkId, $paymentId);
                $this->createOrderFromPendingLink($linkId);
            }
        }

        return response()->json(['received' => true]);
    }

    /**
     * Payment status for the app to poll while the customer pays.
     */
    public function status(Request $request): JsonResponse
    {
        $linkId = (string) $request->query('payment_link_id', '');

        if ($linkId === '') {
            return response()->json(['error' => 'payment_link_id is required.'], 422);
        }

        $pending = Cache::get($this->pendingKey($linkId));
        $orderId = $pending['order_id'] ?? null;
        $status = 'created';

        try {
            $link = $this->api()->paymentLink->fetch($linkId);
            $status = (string) $link->status;

            // If the payment went through but the order hasn't been created
            // yet (webhook lag), create it now so the app can confirm.
            if ($status === 'paid' && ! $orderId) {
                $this->createOrderFromPendingLink($linkId);
                $pending = Cache::get($this->pendingKey($linkId));
                $orderId = $pending['order_id'] ?? null;
            }
        } catch (\Throwable $e) {
            Log::warning('Razorpay status fetch failed', [
                'link_id' => $linkId,
                'error'   => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status'   => $status,
            'order_id' => $orderId,
            'paid'     => $status === 'paid',
        ]);
    }

    /**
     * Create the Bagisto order for a paid payment link, exactly once.
     */
    protected function createOrderFromPendingLink(string $linkId): void
    {
        $key = $this->pendingKey($linkId);
        $pending = Cache::get($key);

        if (! $pending) {
            Log::info('No pending checkout found for payment link', ['link_id' => $linkId]);

            return;
        }

        if (! empty($pending['order_id'])) {
            return; // already placed (callback + webhook may both fire)
        }

        [$items, $subTotal] = $this->checkoutOrder->buildItems($pending['line_items']);

        if (empty($items)) {
            return;
        }

        $paymentId = $pending['razorpay_payment_id'] ?? null;

        $orderData = $this->checkoutOrder->orderData(
            $pending,
            $items,
            $subTotal,
            'razorpay',
            'Razorpay',
            [
                'status'                   => Invoice::STATUS_PAID,
                'razorpay_payment_link_id' => $linkId,
                'razorpay_payment_id'      => $paymentId,
            ],
        );

        $order = $this->orderRepository->create($orderData);

        if ($order->payment) {
            $order->payment->update([
                'additional' => json_encode([
                    'status'                   => Invoice::STATUS_PAID,
                    'razorpay_payment_link_id' => $linkId,
                    'razorpay_payment_id'      => $paymentId,
                ]),
            ]);
        }

        $this->orderRepository->update(['status' => Order::STATUS_PROCESSING], $order->id);

        $invoice = $this->invoiceRepository->create($this->prepareInvoiceData($order->id));

        if ($invoice) {
            $this->orderTransactionRepository->create([
                'transaction_id' => $paymentId ?? $linkId,
                'status'         => 'captured',
                'type'           => 'razorpay',
                'payment_method' => 'razorpay',
                'order_id'       => $order->id,
                'invoice_id'     => $invoice->id,
                'amount'         => $subTotal,
                'data'           => json_encode(['razorpay_payment_link_id' => $linkId]),
            ]);
        }

        $pending['order_id'] = $order->id;
        Cache::put($key, $pending, now()->addSeconds(self::PENDING_TTL_SECONDS));

        Log::info('Order created from Razorpay payment link', [
            'order_id' => $order->id,
            'link_id'  => $linkId,
        ]);
    }

    /**
     * Store the Razorpay payment id on the pending checkout so the order
     * and transaction records can reference it.
     */
    protected function rememberPaymentId(string $linkId, ?string $paymentId): void
    {
        if (! $paymentId) {
            return;
        }

        $key = $this->pendingKey($linkId);
        $pending = Cache::get($key);

        if ($pending && empty($pending['razorpay_payment_id'])) {
            $pending['razorpay_payment_id'] = $paymentId;
            Cache::put($key, $pending, now()->addSeconds(self::PENDING_TTL_SECONDS));
        }
    }

    /**
     * Invoice data for the created order (mirrors the shop Razorpay flow).
     */
    protected function prepareInvoiceData(int $orderId): array
    {
        $order = $this->orderRepository->findOrFail($orderId);

        if (! $order) {
            return [];
        }

        $invoiceItems = [];

        foreach ($order->items as $item) {
            if ($item->qty_to_invoice > 0) {
                $invoiceItems[$item->id] = $item->qty_to_invoice;
            }
        }

        if (empty($invoiceItems)) {
            return [];
        }

        return [
            'order_id' => $order->id,
            'invoice'  => [
                'items' => $invoiceItems,
            ],
        ];
    }

    /**
     * Whether Razorpay credentials are configured (env or admin settings).
     */
    protected function hasCredentials(): bool
    {
        return (bool) $this->keyId() && (bool) $this->keySecret();
    }

    protected function api(): Api
    {
        return new Api($this->keyId(), $this->keySecret());
    }

    protected function keyId(): ?string
    {
        return config('services.razorpay.key') ?: $this->razorpayPayment->getApiKey();
    }

    protected function keySecret(): ?string
    {
        return config('services.razorpay.secret') ?: $this->razorpayPayment->getApiSecret();
    }

    protected function webhookSecret(): ?string
    {
        return config('services.razorpay.webhook_secret') ?: $this->keySecret();
    }

    protected function callbackUrl(): string
    {
        return config('services.razorpay.callback_url') ?: route('api.payments.callback');
    }

    protected function pendingKey(string $linkId): string
    {
        return 'razorpay.pending.'.$linkId;
    }
}
