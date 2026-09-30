<?php

namespace App\Http\Controllers\Admin;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Webkul\Admin\Http\Controllers\Controller;

class GamesController extends Controller
{
    /**
     * Display a listing of games, optionally filtered by search.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $games = Game::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('tagline', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('title')
            ->paginate(20)
            ->withQueryString();

        return view('admin.games.index', [
            'games' => $games,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new game.
     */
    public function create()
    {
        return view('admin.games.create', [
            'icons' => CollectionItemAdminController::ICONS,
        ]);
    }

    /**
     * Store a newly created game.
     */
    public function store(Request $request)
    {
        $validated = $this->validateGame($request);

        Game::create([
            'title' => $validated['title'],
            'tagline' => $validated['tagline'] ?? null,
            'description' => $validated['description'] ?? null,
            'path' => $this->normalizePath($validated['path'] ?? null),
            'tags' => $this->tagsToArray($request->input('tags')),
            'image' => $this->resolveImage($request),
            'accent' => $validated['accent'] ?? null,
            'badge' => $validated['badge'] ?? null,
        ]);

        session()->flash('success', "Game \"{$validated['title']}\" created.");

        return redirect()->route('admin.games.index');
    }

    /**
     * Show the form for editing a game.
     */
    public function edit(int $id)
    {
        $game = Game::findOrFail($id);

        return view('admin.games.edit', [
            'game' => $game,
            'icons' => CollectionItemAdminController::ICONS,
        ]);
    }

    /**
     * Update the specified game.
     */
    public function update(Request $request, int $id)
    {
        $game = Game::findOrFail($id);

        $validated = $this->validateGame($request);

        $game->update([
            'title' => $validated['title'],
            'tagline' => $validated['tagline'] ?? null,
            'description' => $validated['description'] ?? null,
            'path' => $this->normalizePath($validated['path'] ?? null),
            'tags' => $this->tagsToArray($request->input('tags')),
            'image' => $this->resolveImage($request, $game),
            'accent' => $validated['accent'] ?? null,
            'badge' => $validated['badge'] ?? null,
        ]);

        session()->flash('success', "Game \"{$game->title}\" updated.");

        return redirect()->route('admin.games.index');
    }

    /**
     * Remove the specified game.
     */
    public function destroy(int $id)
    {
        $game = Game::findOrFail($id);

        $game->delete();

        session()->flash('success', "Game \"{$game->title}\" deleted.");

        return redirect()->route('admin.games.index');
    }

    /**
     * Validate the game fields.
     */
    protected function validateGame(Request $request): array
    {
        return $this->validate($request, [
            'title' => 'required|max:255',
            'tagline' => 'nullable|max:255',
            'description' => 'nullable',
            'path' => 'nullable|max:255',
            'tags' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
            'remove_image' => 'nullable|boolean',
            'accent' => 'nullable|max:255',
            'badge' => 'nullable|max:255',
        ]);
    }

    /**
     * Store the cover image sent with the form, replacing (and deleting) the
     * previous one. Without an upload the current path is kept, unless the form
     * asked to remove it.
     */
    protected function resolveImage(Request $request, ?Game $game = null): ?string
    {
        $current = $game?->image;

        if ($request->hasFile('image')) {
            $this->deleteImage($current);

            return $request->file('image')->store('games', 'public');
        }

        if ($game && $request->boolean('remove_image')) {
            $this->deleteImage($current);

            return null;
        }

        return $current;
    }

    /**
     * Delete a stored cover image from the public disk.
     */
    protected function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Ensure the playable path starts with a slash and looks like a route.
     */
    protected function normalizePath(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (! str_starts_with($path, '/')) {
            $path = '/'.$path;
        }

        return $path;
    }

    /**
     * Convert a textarea of "Icon|Label" lines into the JSON tags array
     * the React app expects (e.g. [{"icon":"Dices","label":"2-4 players"}]).
     */
    protected function tagsToArray(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        $tags = [];

        foreach (preg_split('/\r\n|\r|\n/', $value) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            // Support both "Icon|Label" and plain "Label" lines.
            if (str_contains($line, '|')) {
                [$icon, $label] = array_map('trim', explode('|', $line, 2));
            } else {
                $icon = null;
                $label = $line;
            }

            if ($label === '') {
                continue;
            }

            $tags[] = $icon ? ['icon' => $icon, 'label' => $label] : ['label' => $label];
        }

        return $tags;
    }
}
