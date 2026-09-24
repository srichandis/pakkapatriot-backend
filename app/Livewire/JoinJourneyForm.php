<?php

namespace App\Livewire;

use App\Models\JourneySubmission;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * The "Become a Buddy" modal.
 *
 * Replaces the hand-written fetch() to POST /api/join-journey in
 * resources/js/app.js with server-side validation and a single Livewire round
 * trip. Submissions land in the same table the JSON API writes to.
 */
class JoinJourneyForm extends Component
{
    public const INTERESTS = [
        '🎨 Local Art',
        '🕌 Historic Monuments',
        '⛰️ Mountains',
        '📚 Freedom Fighters of Bhārat',
        '🧩 Fun Quizzes',
    ];

    public bool $open = false;

    public string $name = '';

    public string $email = '';

    public string $age = '';

    public string $city = '';

    /** @var array<int, string> */
    public array $interests = [];

    public bool $submitted = false;

    #[On('open-journey')]
    public function openModal(): void
    {
        $this->open = true;
        $this->submitted = false;
        $this->resetValidation();
    }

    /**
     * Escape closes the modal (dispatched from resources/js/app.js).
     */
    #[On('close-journey')]
    public function close(): void
    {
        $this->open = false;
    }

    public function toggleInterest(string $interest): void
    {
        if (in_array($interest, $this->interests, true)) {
            $this->interests = array_values(array_diff($this->interests, [$interest]));

            return;
        }

        $this->interests[] = $interest;
    }

    public function submit(): void
    {
        // Same rules as App\Http\Controllers\Api\JourneyApiController.
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'age' => 'nullable|integer|min:1|max:120',
            'city' => 'nullable|string|max:255',
            'interests' => 'nullable|array|max:10',
            'interests.*' => 'string|max:100',
        ]);

        JourneySubmission::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'age' => $validated['age'] !== '' ? $validated['age'] : null,
            'city' => $validated['city'] !== '' ? $validated['city'] : null,
            'interests' => $validated['interests'] ?? [],
        ]);

        $this->submitted = true;
    }

    /**
     * Reset the form after the success state has been read.
     */
    public function resetForm(): void
    {
        $this->reset(['name', 'email', 'age', 'city', 'interests', 'submitted']);
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.join-journey-form', [
            'interestOptions' => self::INTERESTS,
        ]);
    }
}
