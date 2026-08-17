<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterApiController extends Controller
{
    /**
     * Store a newsletter subscription from the website "Let's stay in touch!" form.
     *
     * Expects JSON body: { email, source? }
     */
    public function store(Request $request): JsonResponse
    {
        // Validate manually so failures return JSON 422 regardless of the
        // request's Accept header (the browser fetch does not send it).
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'source' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $subscription = NewsletterSubscription::firstOrCreate(
            ['email' => $validated['email']],
            ['source' => $validated['source'] ?? null],
        );

        return response()->json([
            'success' => true,
            'message' => $subscription->wasRecentlyCreated
                ? 'Subscription saved!'
                : 'You are already subscribed.',
            'id' => $subscription->id,
            'created' => $subscription->wasRecentlyCreated,
        ], $subscription->wasRecentlyCreated ? 201 : 200);
    }
}
