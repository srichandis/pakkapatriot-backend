<?php

namespace App\Livewire;

use App\Models\NewsletterSubscription;
use Livewire\Component;

/**
 * The "Let's stay in touch!" subscription form.
 *
 * Replaces the fetch() to POST /api/newsletter/subscribe in
 * resources/js/app.js. Writes through the same firstOrCreate the JSON API uses,
 * so repeat subscribers are still idempotent.
 */
class NewsletterForm extends Component
{
    public string $email = '';

    public bool $subscribed = false;

    public function subscribe(): void
    {
        $validated = $this->validate([
            'email' => 'required|email|max:255',
        ]);

        NewsletterSubscription::firstOrCreate(
            ['email' => $validated['email']],
            ['source' => 'newsletter-section'],
        );

        $this->subscribed = true;
    }

    public function render()
    {
        return view('livewire.newsletter-form');
    }
}
