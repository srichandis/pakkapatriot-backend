<?php

namespace App\Livewire;

use App\Services\SiteSearch;
use Livewire\Component;

/**
 * The header search box and its live suggestions dropdown.
 *
 * Replaces the fetch() to GET /api/search/suggest plus the manual DOM injection
 * in resources/js/app.js. Suggestions are rendered server-side from the same
 * App\Services\SiteSearch the /search results page uses.
 */
class SiteSearchBox extends Component
{
    public string $query = '';

    public bool $expanded = false;

    public int $highlighted = -1;

    public function mount(): void
    {
        // The results page renders the box already open, pre-filled with ?q=.
        if (request()->is('search')) {
            $this->expanded = true;
            $this->query = (string) (request()->query('q') ?? request()->query('query') ?? '');
        }
    }

    public function toggle(): void
    {
        // With a query in the box the button runs the search instead of closing.
        if ($this->expanded && trim($this->query) !== '') {
            $this->submit();

            return;
        }

        $this->expanded = ! $this->expanded;
        $this->highlighted = -1;
    }

    /**
     * Reset keyboard highlighting whenever the query changes.
     */
    public function updatedQuery(): void
    {
        $this->highlighted = -1;
    }

    public function moveHighlight(int $delta): void
    {
        $suggestions = $this->suggestions();

        if ($suggestions === []) {
            $this->highlighted = -1;

            return;
        }

        $count = count($suggestions);
        $next = $this->highlighted + $delta;

        // Wrap around in both directions.
        $this->highlighted = (($next % $count) + $count) % $count;
    }

    public function clearHighlight(): void
    {
        $this->highlighted = -1;
    }

    /**
     * Enter opens the highlighted suggestion, or runs the full search when
     * nothing is highlighted.
     */
    public function handleEnter(): void
    {
        if ($this->highlighted >= 0) {
            $this->openSuggestion($this->highlighted);

            return;
        }

        $this->submit();
    }

    public function openSuggestion(int $index): void
    {
        $suggestion = $this->suggestions()[$index] ?? null;

        if ($suggestion === null) {
            return;
        }

        $this->redirect($suggestion['url']);
    }

    public function submit(): void
    {
        $query = trim($this->query);

        if ($query === '') {
            return;
        }

        $this->redirect(url('/search').'?q='.rawurlencode($query));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function suggestions(): array
    {
        $query = trim($this->query);

        if (mb_strlen($query) < 2) {
            return [];
        }

        return app(SiteSearch::class)->suggestions($query, 7);
    }

    public function render()
    {
        return view('livewire.site-search-box', [
            'suggestions' => $this->suggestions(),
        ]);
    }
}
