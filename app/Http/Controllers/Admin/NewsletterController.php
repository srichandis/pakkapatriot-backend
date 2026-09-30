<?php

namespace App\Http\Controllers\Admin;

use App\Mail\NewsletterMail;
use App\Models\JourneySubmission;
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
        $recipients = $this->leadRecipients();

        return view('admin.newsletter.compose', [
            'recipients' => $recipients,
            'total' => count($recipients),
        ]);
    }

    /**
     * Send the newsletter to the recipients chosen in the compose form.
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|email|max:255',
        ]);

        // De-duplicate case-insensitively (a lead can appear in both the
        // newsletter and journey tables) and drop anything malformed.
        $recipients = collect($validated['recipients'])
            ->map(fn ($email) => trim((string) $email))
            ->filter(fn ($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique(fn ($email) => mb_strtolower($email))
            ->values();

        if ($recipients->isEmpty()) {
            return redirect()
                ->route('admin.newsletter.compose')
                ->with('error', 'No valid recipients to send to.')
                ->withInput();
        }

        $subject = $validated['subject'];
        $body = $validated['body'];
        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipients as $email) {
            try {
                Mail::to($email)
                    ->send(new NewsletterMail($subject, $body));
                $sentCount++;
            } catch (\Throwable $e) {
                $failedCount++;
                \Log::error("Newsletter send failed for {$email}: {$e->getMessage()}");
            }
        }

        $message = "Newsletter sent to {$sentCount} recipient(s).";
        if ($failedCount > 0) {
            $message .= " {$failedCount} failed.";
        }

        return redirect()
            ->route('admin.newsletter.index')
            ->with('success', $message);
    }

    /**
     * Every email collected by the site's lead forms — the "Let's stay in
     * touch!" newsletter form and the "Join the Journey" form — de-duplicated
     * case-insensitively and sorted.
     *
     * @return array<int, string>
     */
    protected function leadRecipients(): array
    {
        return NewsletterSubscription::query()
            ->pluck('email')
            ->merge(JourneySubmission::query()->pluck('email'))
            ->map(fn ($email) => trim((string) $email))
            ->filter(fn ($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique(fn ($email) => mb_strtolower($email))
            ->sort()
            ->values()
            ->all();
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
