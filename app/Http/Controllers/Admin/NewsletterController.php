<?php

namespace App\Http\Controllers\Admin;

use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Webkul\Admin\Http\Controllers\Controller;

class NewsletterController extends Controller
{
    /**
     * Display a listing of newsletter subscriptions.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $subscriptions = NewsletterSubscription::query()
            ->when($search !== '', fn ($query) => $query->search($search))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.newsletter.index', [
            'subscriptions' => $subscriptions,
            'search' => $search,
            'total' => NewsletterSubscription::count(),
        ]);
    }

    /**
     * Remove the specified subscription.
     */
    public function destroy(int $id)
    {
        $subscription = NewsletterSubscription::findOrFail($id);

        $subscription->delete();

        session()->flash('success', "Subscription for \"{$subscription->email}\" deleted.");

        return redirect()->route('admin.newsletter.index');
    }
}
