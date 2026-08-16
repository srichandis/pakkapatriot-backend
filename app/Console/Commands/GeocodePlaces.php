<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GeocodePlaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geocode:places';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill latitude/longitude for Places collection items using OpenStreetMap Nominatim';

    /**
     * National symbols and non-geographic items have no meaningful pin.
     */
    protected array $skip = [
        'ashoka-chakra',
        'independence-day',
        'national-animal-tiger',
        'national-anthem',
        'national-bird-peacock',
        'national-emblem',
        'national-flag',
        'national-flower-lotus',
        'national-pledge',
        'national-tree-banyan',
        'republic-day',
        'saare-jahan-se-achha',
        'vande-mataram',
    ];

    /**
     * Hand-tuned queries for places where the name/region wording confuses
     * Nominatim. Values are tried before the generic fallbacks.
     */
    protected array $queryOverrides = [
        'belum-caves' => 'Belum Caves Andhra Pradesh',
        'borra-caves' => 'Borra Caves Visakhapatnam',
        'dudhsagar-falls' => 'Dudhsagar Falls Goa',
        'kerala-backwaters' => 'Alappuzha Kerala',
        'living-root-bridges' => 'Nongriat Meghalaya',
        'magnetic-hill' => 'Magnetic Hill Ladakh',
        'rishikesh' => 'Rishikesh Uttarakhand',
        'st-marys-islands' => 'St Marys Islands Karnataka',
        'statue-of-unity' => 'Statue of Unity Gujarat',
        'sundarbans' => 'Sundarbans National Park',
        'valley-of-flowers' => 'Valley of Flowers National Park Uttarakhand',
        'varanasi' => 'Varanasi Uttar Pradesh',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $places = DB::table('collection_items')
            ->where('type', 'places')
            ->whereNull('latitude')
            ->orderBy('slug')
            ->get();

        if ($places->isEmpty()) {
            $this->components->info('Nothing to geocode — every place already has coordinates.');

            return Command::SUCCESS;
        }

        $geocoded = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($places as $place) {
            if (in_array($place->slug, $this->skip, true)) {
                $this->components->twoColumnDetail("  – Skipped (no pin): {$place->name}", $place->slug);
                $skipped++;

                continue;
            }

            $result = $this->geocode($place->name, $place->region, $place->slug);

            if ($result) {
                DB::table('collection_items')
                    ->where('id', $place->id)
                    ->update([
                        'latitude' => $result['lat'],
                        'longitude' => $result['lon'],
                    ]);
                $this->components->twoColumnDetail(
                    "  ✓ {$place->name}",
                    "{$result['lat']}, {$result['lon']}"
                );
                $geocoded++;
            } else {
                $this->components->twoColumnDetail("  ✗ Not found: {$place->name}", $place->slug);
                $failed++;
            }

            // Nominatim usage policy: max 1 request/second.
            sleep(1);
        }

        $this->newLine();
        $this->components->info('Geocode Summary');
        $this->newLine();
        $this->components->twoColumnDetail('Geocoded', (string) $geocoded);
        $this->components->twoColumnDetail('Skipped (no pin)', (string) $skipped);
        $this->components->twoColumnDetail('Not Found', (string) $failed);
        $this->newLine();

        return Command::SUCCESS;
    }

    /**
     * Resolve coordinates for a place via the Nominatim search API.
     *
     * @return array{lat: float, lon: float}|null
     */
    protected function geocode(string $name, ?string $region, string $slug = ''): ?array
    {
        $queries = array_values(array_filter([
            $this->queryOverrides[$slug] ?? '',
            trim($name.', '.($region ?? '')),
            trim($region ?? ''),
            trim($name),
        ]));

        foreach ($queries as $query) {
            try {
                $response = Http::timeout(15)
                    ->withHeaders([
                        'User-Agent' => 'PakkaPatriot/1.0 (contact: hello@pakkapatriot.com)',
                        'Accept' => 'application/json',
                    ])
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'q' => $query,
                        'format' => 'json',
                        'limit' => 1,
                        'countrycodes' => 'in',
                    ]);

                if (! $response->ok()) {
                    continue;
                }

                $results = $response->json();

                if (is_array($results) && isset($results[0]['lat'], $results[0]['lon'])) {
                    return [
                        'lat' => (float) $results[0]['lat'],
                        'lon' => (float) $results[0]['lon'],
                    ];
                }
            } catch (\Throwable $e) {
                $this->components->twoColumnDetail('  ⚠ Nominatim error', $e->getMessage());

                return null;
            }
        }

        return null;
    }
}
