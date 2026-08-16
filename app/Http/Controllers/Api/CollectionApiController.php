<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CollectionController;
use App\Models\CollectionItem;
use App\Models\CreateActivity;
use App\Models\EBook;
use App\Models\Game;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CollectionApiController extends Controller
{
    /**
     * Return every knowledge collection + games + ebooks + activities as JSON,
     * mirroring the shape the React app expects (icons as names, camelCase keys).
     */
    public function data(): JsonResponse
    {
        $collections = collect((new CollectionController)->collections)
            ->map(fn (array $meta, string $type) => [
                'meta' => $meta,
                'items' => CollectionItem::ofType($type)->get()->map(fn (CollectionItem $i) => $this->item($i))->values(),
            ])
            ->all();

        return response()->json([
            'collections' => $collections,
            'games' => Game::orderBy('title')->get()->map(fn (Game $g) => [
                'id' => $g->id,
                'title' => $g->title,
                'tagline' => $g->tagline,
                'description' => $g->description,
                'path' => $g->path,
                'tags' => $g->tags ?? [],
                'accent' => $g->accent,
                'badge' => $g->badge,
            ])->values(),
            'ebooks' => EBook::orderBy('title')->get()->map(fn (EBook $e) => [
                'id' => $e->id,
                'title' => $e->title,
                'subtitle' => $e->subtitle,
                'category' => $e->category,
                'era' => $e->era,
                'description' => $e->description,
                'coverColor' => $e->cover_color,
                'coverEmoji' => $e->cover_emoji,
            ])->values(),
            'activities' => CreateActivity::orderBy('title')->get()->map(fn (CreateActivity $a) => [
                'slug' => $a->slug,
                'badge' => $a->badge,
                'title' => $a->title,
                'emoji' => $a->emoji,
                'tagline' => $a->tagline,
                'whatIs' => $a->what_is,
                'knownFor' => $a->known_for ?? [],
                'tryThis' => $a->try_this ?? [],
                'related' => $a->related ?? [],
                'heroAccent' => $a->hero_accent,
                'tile' => $a->tile,
                'button' => $a->button,
            ])->values(),
        ]);
    }

    private function item(CollectionItem $i): array
    {
        return [
            'slug' => $i->slug,
            'name' => $i->name,
            'nativeName' => $i->native_name,
            'tagline' => $i->tagline,
            'category' => $i->category,
            'era' => $i->era,
            'attribution' => $i->attribution,
            'region' => $i->region,
            'latitude' => $i->latitude,
            'longitude' => $i->longitude,
            'icon' => $i->icon,
            'accent' => $i->accent,
            'softAccent' => $i->soft_accent,
            'iconColor' => $i->icon_color,
            'quote' => $i->quote,
            'quoteSource' => $i->quote_source,
            'summary' => $i->summary,
            'overview' => $i->overview ?? [],
            'coreIdeas' => $i->core_ideas ?? [],
            'legacy' => $i->legacy,
        ];
    }
}
