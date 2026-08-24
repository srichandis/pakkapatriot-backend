<?php

namespace App\Http\Controllers\Admin;

use App\Mail\NewsletterMail;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
     * Show the newsletter compose form.
     */
    public function compose()
    {
        $total = NewsletterSubscription::count();

        return view('admin.newsletter.compose', [
            'total' => $total,
        ]);
    }

    /**
     * Send the newsletter to all subscribers.
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        $subscribers = NewsletterSubscription::all();

        if ($subscribers->isEmpty()) {
            return redirect()
                ->route('admin.newsletter.compose')
                ->with('error', 'No subscribers to send to.');
        }

        $subject = $validated['subject'];
        $body = $validated['body'];
        $sentCount = 0;
        $failedCount = 0;

        foreach ($subscribers as $subscriber) {
            try {
                Mail::to($subscriber->email)
                    ->send(new NewsletterMail($subject, $body));
                $sentCount++;
            } catch (\Throwable $e) {
                $failedCount++;
                \Log::error("Newsletter send failed for {$subscriber->email}: {$e->getMessage()}");
            }
        }

        $message = "Newsletter sent to {$sentCount} subscriber(s).";
        if ($failedCount > 0) {
            $message .= " {$failedCount} failed.";
        }

        return redirect()
            ->route('admin.newsletter.index')
            ->with('success', $message);
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
