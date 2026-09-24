<?php

namespace App\Http\Controllers;

use App\Data\CreateCards;
use App\Models\CreateActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

/**
 * CREATE — the Pakka Patriot maker's space, and one page per maker activity.
 *
 * Ported from the React CreatePage / CreateActivityPage. Card labels come from
 * App\Data\CreateCards; the detail pages render the activity rows in the
 * `create_activities` table.
 */
class CreateController extends Controller
{
    public function index(): View
    {
        return view('create.index', [
            'cards' => CreateCards::all(),
            'processSteps' => CreateCards::processSteps(),
            'heroLetters' => CreateCards::heroLetters(),
            'creations' => CreateCards::creations(),
            'printables' => CreateCards::printables(),
        ]);
    }

    public function activity(string $slug): View|Response
    {
        $activity = CreateActivity::where('slug', $slug)->first();

        // React showed a friendly "still being crafted" page for an unknown
        // slug. Keep that page but answer 404, so crawlers treat it correctly.
        return $activity
            ? view('create.activity', compact('activity'))
            : response()->view('create.activity', ['activity' => null], 404);
    }
}
