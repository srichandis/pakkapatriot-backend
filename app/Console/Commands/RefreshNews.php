<?php

namespace App\Console\Commands;

use App\Services\NewsFeed;
use Illuminate\Console\Command;

/**
 * Refills the /news cache from the configured RSS topics. Scheduled daily in
 * bootstrap/app.php so the page is rebuilt every morning without waiting on
 * the publishers at request time.
 */
class RefreshNews extends Command
{
    protected $signature = 'news:refresh';

    protected $description = 'Refresh the cached news feed for the /news page';

    public function handle(NewsFeed $feed): int
    {
        $items = $feed->refresh();

        if ($items === []) {
            $this->error('No headlines fetched — check the feeds in config/news.php.');

            return self::FAILURE;
        }

        $byTopic = collect($items)->countBy('topic');

        $this->info(count($items).' headlines cached from '.$byTopic->count().' topics:');

        foreach ($byTopic as $topic => $count) {
            $this->line("  {$topic}: {$count}");
        }

        return self::SUCCESS;
    }
}
