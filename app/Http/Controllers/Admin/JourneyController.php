<?php

namespace App\Http\Controllers\Admin;

use App\Models\JourneySubmission;
use Illuminate\Http\Request;
use Webkul\Admin\Http\Controllers\Controller;

class JourneyController extends Controller
{
    /**
     * Display a listing of journey submissions.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $submissions = JourneySubmission::query()
            ->when($search !== '', fn ($query) => $query->search($search))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.journey.index', [
            'submissions' => $submissions,
            'search' => $search,
            'total' => JourneySubmission::count(),
        ]);
    }

    /**
     * Remove the specified submission.
     */
    public function destroy(int $id)
    {
        $submission = JourneySubmission::findOrFail($id);

        $submission->delete();

        session()->flash('success', "Submission from \"{$submission->name}\" deleted.");

        return redirect()->route('admin.journey.index');
    }
}
