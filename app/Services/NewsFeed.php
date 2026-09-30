<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Collects the news headlines shown on /news.
 *
 * Every topic in config/news.php is fetched as RSS, merged, de-duplicated and
 * put in the cache under a single key. The page never calls a publisher
 * directly: it reads the cache, and the cache is (re)filled by the daily
 * `news:refresh` command. A visit that finds the cache stale refreshes it
 * itself, under a lock, so a burst of traffic cannot turn into a burst of
 * outbound requests.
 */
class NewsFeed
{
    /**
     * Cache key holding ['fetched_at' => string, 'items' => array].
     */
    public const CACHE_KEY = 'pp.news.feed';

    /**
     * Headlines ready to render, oldest-refresh tolerated.
     *
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $cached = Cache::get(self::CACHE_KEY);

        if ($this->isFresh($cached)) {
            return $cached['items'];
        }

        // Somebody else may already be refreshing — serve the stale copy
        // rather than pile another six requests onto the network.
        $lock = Cache::lock(self::CACHE_KEY.'.lock', 120);

        if (! $lock->get()) {
            return $cached['items'] ?? [];
        }

        try {
            return $this->refresh();
        } finally {
            $lock->release();
        }
    }

    /**
     * Fetch every topic, merge, and replace the cache.
     *
     * Returns the previous payload when the network gives us nothing, so a
     * failed run never blanks the page.
     *
     * @return array<int, array<string, mixed>>
     */
    public function refresh(): array
    {
        $items = $this->collect();

        if ($items === []) {
            return Cache::get(self::CACHE_KEY)['items'] ?? [];
        }

        Cache::put(self::CACHE_KEY, [
            'fetched_at' => CarbonImmutable::now()->toIso8601String(),
            'items' => $items,
        ], CarbonImmutable::now()->addHours((int) config('news.ttl_hours', 20) * 2));

        return $items;
    }

    /**
     * When the cache was last filled, or null if it never has been.
     */
    public function refreshedAt(): ?CarbonImmutable
    {
        $cached = Cache::get(self::CACHE_KEY);

        return isset($cached['fetched_at']) ? CarbonImmutable::parse($cached['fetched_at']) : null;
    }

    /**
     * The topics the page offers as filters.
     *
     * @return array<int, string>
     */
    public function topics(): array
    {
        return array_keys((array) config('news.topics', []));
    }

    /**
     * Pull every topic and normalise the lot.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function collect(): array
    {
        $cutoff = CarbonImmutable::now()->subDays((int) config('news.max_age_days', 30));
        $perTopic = (int) config('news.per_topic', 12);

        $items = [];

        foreach ((array) config('news.topics', []) as $topic => $source) {
            // Newest first within the topic, then trimmed to its quota — a
            // busy topic must not crowd out the rest of the page.
            $fetched = array_filter(
                $this->fetchTopic((string) $source),
                fn (array $item) => $item['published_at'] >= $cutoff
            );

            usort($fetched, fn (array $a, array $b) => $b['published_at'] <=> $a['published_at']);

            foreach (array_slice($fetched, 0, $perTopic) as $item) {
                $key = $this->dedupeKey($item);

                // First topic to claim a headline keeps it; later duplicates
                // from a neighbouring topic are dropped.
                if (! isset($items[$key])) {
                    $item['topic'] = $topic;
                    $items[$key] = $item;
                }
            }
        }

        uasort($items, fn (array $a, array $b) => $b['published_at'] <=> $a['published_at']);

        return array_slice(array_values($items), 0, (int) config('news.limit', 60));
    }

    /**
     * Fetch and parse one topic. Any failure degrades to an empty list so one
     * dead feed cannot take the page down.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function fetchTopic(string $source): array
    {
        try {
            $response = Http::timeout(15)
                ->retry(2, 400, throw: false)
                ->withHeaders(['User-Agent' => 'PakkaPatriot-News/1.0 (+https://pakkapatriot.com)'])
                ->get($this->url($source));

            if (! $response->ok()) {
                return [];
            }
        } catch (\Throwable $e) {
            report($e);

            return [];
        }

        return $this->parse($response->body());
    }

    /**
     * The RSS URL for a topic — a search query, or a feed URL used verbatim.
     */
    protected function url(string $source): string
    {
        if (Str::startsWith($source, 'http')) {
            return $source;
        }

        $edition = (array) config('news.edition', []);

        return 'https://news.google.com/rss/search?'.http_build_query([
            'q' => $source,
            'hl' => $edition['hl'] ?? 'en-IN',
            'gl' => $edition['gl'] ?? 'IN',
            'ceid' => $edition['ceid'] ?? 'IN:en',
        ]);
    }

    /**
     * Turn an RSS or Atom document into normalised items.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function parse(string $xml): array
    {
        $previous = libxml_use_internal_errors(true);

        // LIBXML_NONET keeps entity resolution off the network.
        $document = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET);

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ($document === false) {
            return [];
        }

        // RSS 2.0 nests items under <channel>; Atom puts entries at the root.
        $nodes = $document->channel->item ?? $document->entry ?? [];

        $items = [];

        foreach ($nodes as $node) {
            $item = $this->normalise($node);

            if ($item !== null) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function normalise(\SimpleXMLElement $node): ?array
    {
        $title = $this->text($node->title);
        $link = $this->link($node);

        if ($title === '' || $link === '') {
            return null;
        }

        $source = $this->text($node->source);

        // Google News titles arrive as "Headline - Publisher"; drop the
        // publisher tail, which the card shows as the source anyway.
        if ($source !== '' && Str::endsWith($title, ' - '.$source)) {
            $title = Str::beforeLast($title, ' - '.$source);
        }

        $published = $this->date($node);

        if ($published === null) {
            return null;
        }

        $sourceUrl = null;
        if (isset($node->source)) {
            foreach ($node->source->attributes() as $name => $value) {
                if ($name === 'url') {
                    $sourceUrl = (string) $value;
                }
            }
        }

        return [
            'title' => $title,
            'url' => $link,
            'source' => $source !== '' ? $source : parse_url($link, PHP_URL_HOST),
            'source_url' => $sourceUrl,
            'published_at' => $published,
        ];
    }

    /**
     * The item's link — RSS uses a text node, Atom an href attribute.
     */
    protected function link(\SimpleXMLElement $node): string
    {
        $link = $this->text($node->link);

        if ($link !== '') {
            return $link;
        }

        if (isset($node->link)) {
            foreach ($node->link->attributes() as $name => $value) {
                if ($name === 'href') {
                    return (string) $value;
                }
            }
        }

        // Google News sets <guid> to the article id used in the link.
        return $this->text($node->guid);
    }

    /**
     * Published date, from RSS pubDate or Atom updated/published.
     */
    protected function date(\SimpleXMLElement $node): ?CarbonImmutable
    {
        foreach (['pubDate', 'published', 'updated', 'dc:date'] as $field) {
            $value = $this->text($node->{$field} ?? null);

            if ($value === '') {
                continue;
            }

            try {
                return CarbonImmutable::parse($value);
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    /**
     * Stable key for de-duplication: the headline, minus punctuation and case.
     *
     * Two publishers covering the same story word their headlines
     * differently, so this only catches exact syndication repeats — which is
     * the common case across Google News topics.
     */
    protected function dedupeKey(array $item): string
    {
        return Str::of($item['title'])->lower()->replaceMatches('/[^a-z0-9]+/', ' ')->trim()->value();
    }

    protected function text(mixed $value): string
    {
        return trim(html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5));
    }

    /**
     * Is the cached payload present and still within its lifetime?
     */
    protected function isFresh(mixed $cached): bool
    {
        if (! is_array($cached) || empty($cached['items']) || empty($cached['fetched_at'])) {
            return false;
        }

        try {
            $fetchedAt = CarbonImmutable::parse($cached['fetched_at']);
        } catch (\Throwable) {
            return false;
        }

        return $fetchedAt->addHours((int) config('news.ttl_hours', 20))->isFuture();
    }
}
