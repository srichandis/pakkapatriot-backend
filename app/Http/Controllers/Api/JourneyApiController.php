<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JourneySubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JourneyApiController extends Controller
{
    /**
     * Store a "Join the Journey" form submission from the frontend.
     *
     * Expects JSON body: { name, email, age?, city?, interests?: string[] }
     */
    public function store(Request $request): JsonResponse
    {
        // Validate manually so failures return JSON 422 regardless of the
        // request's Accept header (the browser fetch does not send it).
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'age' => 'nullable|integer|min:1|max:120',
            'city' => 'nullable|string|max:255',
            'interests' => 'nullable|array|max:10',
            'interests.*' => 'string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $submission = JourneySubmission::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'age' => $validated['age'] ?? null,
            'city' => $validated['city'] ?? null,
            'interests' => $validated['interests'] ?? [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Welcome aboard!',
            'id' => $submission->id,
        ], 201);
    }
}
