<?php

namespace Database\Seeders;

use App\Models\CollectionItem;
use App\Models\Game;
use App\Models\EBook;
use App\Models\CreateActivity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CollectionDataSeeder extends Seeder
{
    /**
     * Imports data from database/seeders/data/pp-data.json — a snapshot extracted from the
     * React app's src/data/*.ts files (via a tsx script). Re-extract + re-run this seeder
     * whenever the source data changes.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/pp-data.json');

        if (! File::exists($path)) {
            $this->command->error('pp-data.json not found at ' . $path);

            return;
        }

        $data = json_decode(File::get($path), true);

        $count = 0;

        foreach ($data['collections'] ?? [] as $type => $collection) {
            foreach ($collection['items'] ?? [] as $item) {
                CollectionItem::updateOrCreate(
                    ['type' => $type, 'slug' => $item['slug']],
                    [
                        'name' => $item['name'] ?? null,
                        'native_name' => $item['nativeName'] ?? null,
                        'tagline' => $item['tagline'] ?? null,
                        'category' => $item['category'] ?? null,
                        'era' => $item['era'] ?? null,
                        'attribution' => $item['attribution'] ?? null,
                        'region' => $item['region'] ?? null,
                        'icon' => $item['icon'] ?? null,
                        'accent' => $item['accent'] ?? null,
                        'soft_accent' => $item['softAccent'] ?? null,
                        'icon_color' => $item['iconColor'] ?? null,
                        'quote' => $item['quote'] ?? null,
                        'quote_source' => $item['quoteSource'] ?? null,
                        'summary' => $item['summary'] ?? null,
                        'overview' => $item['overview'] ?? [],
                        'core_ideas' => $item['coreIdeas'] ?? [],
                        'legacy' => $item['legacy'] ?? null,
                    ]
                );
                $count++;
            }
        }

        foreach ($data['games'] ?? [] as $game) {
            Game::updateOrCreate(
                ['title' => $game['title']],
                [
                    'tagline' => $game['tagline'] ?? null,
                    'description' => $game['description'] ?? null,
                    'path' => $game['path'] ?? null,
                    'tags' => $game['tags'] ?? [],
                    'accent' => $game['accent'] ?? null,
                    'badge' => $game['badge'] ?? null,
                ]
            );
        }

        foreach ($data['ebooks'] ?? [] as $ebook) {
            EBook::updateOrCreate(
                ['id' => $ebook['id']],
                [
                    'title' => $ebook['title'] ?? null,
                    'subtitle' => $ebook['subtitle'] ?? null,
                    'category' => $ebook['category'] ?? null,
                    'era' => $ebook['era'] ?? null,
                    'description' => $ebook['description'] ?? null,
                    'cover_color' => $ebook['coverColor'] ?? null,
                    'cover_emoji' => $ebook['coverEmoji'] ?? null,
                ]
            );
        }

        foreach ($data['activities'] ?? [] as $activity) {
            CreateActivity::updateOrCreate(
                ['slug' => $activity['slug']],
                [
                    'badge' => $activity['badge'] ?? null,
                    'title' => $activity['title'] ?? null,
                    'emoji' => $activity['emoji'] ?? null,
                    'tagline' => $activity['tagline'] ?? null,
                    'what_is' => $activity['whatIs'] ?? null,
                    'known_for' => $activity['knownFor'] ?? [],
                    'try_this' => $activity['tryThis'] ?? [],
                    'related' => $activity['related'] ?? [],
                    'hero_accent' => $activity['heroAccent'] ?? null,
                    'tile' => $activity['tile'] ?? null,
                    'button' => $activity['button'] ?? null,
                ]
            );
        }

        $this->command->info("Pakka Patriot data seeded: {$count} collection items.");
    }
}
