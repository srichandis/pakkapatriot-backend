<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CollectionController;
use App\Models\CollectionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Webkul\Admin\Http\Controllers\Controller;

abstract class CollectionItemAdminController extends Controller
{
    /**
     * Collection type managed by the concrete controller (e.g. 'ideas', 'places').
     */
    protected string $type;

    /**
     * Valid lucide icon names (must match src/services/iconMap.ts in the React app).
     */
    const ICONS = [
        'Anchor', 'Atom', 'Award', 'Bird', 'Bomb', 'BookOpen', 'Bot', 'Brain',
        'Brush', 'Building2', 'Calculator', 'Candy', 'Castle', 'ChefHat',
        'CircleDot', 'Clapperboard', 'ClipboardCheck', 'Compass', 'Cone',
        'Crown', 'Diamond', 'Dices', 'Disc', 'Drama', 'Droplets', 'Drum', 'Eye',
        'Factory', 'Feather', 'Flag', 'Flame', 'FlaskConical', 'Flower',
        'Flower2', 'Footprints', 'Gem', 'Globe', 'GraduationCap', 'Guitar',
        'Hammer', 'Hand', 'Heart', 'House', 'Infinity', 'Landmark', 'Layers',
        'LayoutGrid', 'Leaf', 'Lightbulb', 'Magnet', 'Mic', 'Microscope',
        'Monitor', 'Moon', 'Mountain', 'MountainSnow', 'MoveUpRight', 'Music',
        'Music2', 'Music3', 'Orbit', 'Paintbrush', 'Palette', 'PawPrint',
        'PenTool', 'PersonStanding', 'Quote', 'Rocket', 'Sailboat', 'Scale',
        'Scroll', 'Shell', 'Shield', 'Ship', 'Shirt', 'Skull', 'Snowflake',
        'Sparkles', 'Sprout', 'Star', 'Stethoscope', 'Sun', 'Sunrise', 'Sunset',
        'Swords', 'Target', 'Theater', 'TreeDeciduous', 'TreePine', 'Trees',
        'Trophy', 'Utensils', 'Users', 'Waves', 'Wifi', 'Zap',
    ];

    /**
     * Display a listing of items, optionally filtered by search/category.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $category = $request->query('category');

        $items = CollectionItem::ofType($this->type)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('native_name', 'like', "%{$search}%")
                        ->orWhere('region', 'like', "%{$search}%")
                        ->orWhere('tagline', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when($category, fn ($query) => $query->where('category', $category))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.collections.index', [
            'type' => $this->type,
            'meta' => $this->meta(),
            'items' => $items,
            'categories' => $this->categories(),
            'search' => $search,
            'category' => $category,
        ]);
    }

    /**
     * Show the form for creating a new item.
     */
    public function create()
    {
        return view('admin.collections.create', [
            'type' => $this->type,
            'meta' => $this->meta(),
            'categories' => $this->categories(),
            'icons' => self::ICONS,
            'accents' => $this->accents(),
        ]);
    }

    /**
     * Store a newly created item.
     */
    public function store(Request $request)
    {
        $validated = $this->validateItem($request);

        $item = CollectionItem::create([
            'type' => $this->type,
            'slug' => $this->slugify($validated['name'], $validated['slug'] ?? null),
            'name' => $validated['name'],
            'native_name' => $validated['native_name'] ?? null,
            'tagline' => $validated['tagline'] ?? null,
            'category' => $validated['category'] ?? null,
            'era' => $validated['era'] ?? null,
            'attribution' => $validated['attribution'] ?? null,
            'region' => $validated['region'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'accent' => $validated['accent'] ?? null,
            'soft_accent' => $validated['soft_accent'] ?? null,
            'icon_color' => $validated['icon_color'] ?? null,
            'quote' => $validated['quote'] ?? null,
            'quote_source' => $validated['quote_source'] ?? null,
            'summary' => $validated['summary'] ?? null,
            'overview' => $this->linesToArray($request->input('overview')),
            'core_ideas' => $this->linesToArray($request->input('core_ideas'), true),
            'legacy' => $validated['legacy'] ?? null,
        ]);

        session()->flash('success', ucfirst($this->type)." item \"{$item->name}\" created.");

        return redirect()->route('admin.'.$this->type.'.index');
    }

    /**
     * Show the form for editing an item.
     */
    public function edit(int $id)
    {
        $item = CollectionItem::ofType($this->type)->findOrFail($id);

        return view('admin.collections.edit', [
            'type' => $this->type,
            'meta' => $this->meta(),
            'item' => $item,
            'categories' => $this->categories(),
            'icons' => self::ICONS,
            'accents' => $this->accents(),
        ]);
    }

    /**
     * Update the specified item.
     */
    public function update(Request $request, int $id)
    {
        $item = CollectionItem::ofType($this->type)->findOrFail($id);

        $validated = $this->validateItem($request, $item);

        $item->update([
            'slug' => $this->slugify($validated['name'], $validated['slug'] ?? null, $item),
            'name' => $validated['name'],
            'native_name' => $validated['native_name'] ?? null,
            'tagline' => $validated['tagline'] ?? null,
            'category' => $validated['category'] ?? null,
            'era' => $validated['era'] ?? null,
            'attribution' => $validated['attribution'] ?? null,
            'region' => $validated['region'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'accent' => $validated['accent'] ?? null,
            'soft_accent' => $validated['soft_accent'] ?? null,
            'icon_color' => $validated['icon_color'] ?? null,
            'quote' => $validated['quote'] ?? null,
            'quote_source' => $validated['quote_source'] ?? null,
            'summary' => $validated['summary'] ?? null,
            'overview' => $this->linesToArray($request->input('overview')),
            'core_ideas' => $this->linesToArray($request->input('core_ideas'), true),
            'legacy' => $validated['legacy'] ?? null,
        ]);

        session()->flash('success', ucfirst($this->type)." item \"{$item->name}\" updated.");

        return redirect()->route('admin.'.$this->type.'.index');
    }

    /**
     * Remove the specified item.
     */
    public function destroy(int $id)
    {
        $item = CollectionItem::ofType($this->type)->findOrFail($id);

        $item->delete();

        session()->flash('success', ucfirst($this->type)." item \"{$item->name}\" deleted.");

        return redirect()->route('admin.'.$this->type.'.index');
    }

    /**
     * Validate the common item fields.
     */
    protected function validateItem(Request $request, ?CollectionItem $ignore = null): array
    {
        $slugRule = ['nullable', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'];
        $slugRule[] = \Illuminate\Validation\Rule::unique('collection_items', 'slug')
            ->where('type', $this->type)
            ->ignore($ignore?->id);

        return $this->validate($request, [
            'name' => 'required|max:255',
            'slug' => $slugRule,
            'native_name' => 'nullable|max:255',
            'tagline' => 'nullable|max:255',
            'category' => 'nullable|max:255',
            'era' => 'nullable|max:255',
            'attribution' => 'nullable|max:255',
            'region' => 'nullable|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'icon' => 'nullable|max:255',
            'accent' => 'nullable|max:255',
            'soft_accent' => 'nullable|max:255',
            'icon_color' => 'nullable|max:255',
            'quote' => 'nullable|max:255',
            'quote_source' => 'nullable|max:255',
            'summary' => 'nullable',
            'overview' => 'nullable',
            'core_ideas' => 'nullable',
            'legacy' => 'nullable',
        ]);
    }

    /**
     * Build a unique slug from the provided value (or from the name).
     */
    protected function slugify(string $name, ?string $slug, ?CollectionItem $ignore = null): string
    {
        $base = Str::slug($slug ?: $name, '-');

        if ($base === '') {
            $base = Str::slug($this->type).'-'.Str::lower(Str::random(6));
        }

        $candidate = $base;
        $i = 2;
        while (CollectionItem::ofType($this->type)
            ->where('slug', $candidate)
            ->when($ignore, fn ($q) => $q->where('id', '!=', $ignore->id))
            ->exists()) {
            $candidate = $base.'-'.$i;
            $i++;
        }

        return $candidate;
    }

    /**
     * Split a textarea value into an array (one item per line). Lines in the
     * "Title|Text" form become objects (used by core_ideas cards); plain lines
     * stay strings.
     */
    protected function linesToArray(?string $value, bool $objects = false): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        $items = array_values(array_filter(
            array_map('trim', preg_split('/\r\n|\r|\n/', $value)),
            fn ($line) => $line !== ''
        ));

        if (! $objects) {
            return $items;
        }

        return array_map(function (string $line) {
            if (str_contains($line, '|')) {
                [$title, $text] = array_map('trim', explode('|', $line, 2));

                return ['title' => $title, 'text' => $text];
            }

            return ['title' => '', 'text' => $line];
        }, $items);
    }

    /**
     * Metadata for this collection type (mirrors the React app's registry).
     */
    protected function meta(): array
    {
        return (new CollectionController)->collections[$this->type] ?? [];
    }

    /**
     * Categories configured for this collection type.
     */
    protected function categories(): array
    {
        return $this->meta()['categories'] ?? [];
    }

    /**
     * Distinct accent gradients already used by this type (quick-pick suggestions).
     */
    protected function accents(): array
    {
        return CollectionItem::ofType($this->type)
            ->whereNotNull('accent')
            ->distinct()
            ->pluck('accent')
            ->values()
            ->all();
    }
}
