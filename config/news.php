<?php

/*
|--------------------------------------------------------------------------
| News
|--------------------------------------------------------------------------
|
| The news page collects headlines about the country's achievements from RSS.
| Each topic below becomes one query against Google News' RSS search, which
| aggregates thousands of Indian publishers (PIB, ISRO, The Hindu, PTI…) into
| the same feed shape.
|
| A topic's value may also be a full RSS/Atom URL — anything starting with
| "http" is fetched as-is instead of being turned into a search. That is how
| to add a publisher's own feed, e.g. 'PIB' => 'https://www.pib.gov.in/...'.
|
| The feed is cached (see App\Services\NewsFeed) and refreshed by the daily
| `news:refresh` command, so the page is rebuilt every day without a visit
| having to wait on six HTTP calls.
|
*/

return [

    'topics' => [
        'Space & Science' => 'India ISRO space mission success',
        'Sport' => 'India medal world championship win',
        'Innovation' => 'India innovation technology breakthrough',
        'Global Recognition' => 'India international award honour recognition',
        'Defence' => 'India indigenous defence system',
        'Culture & Heritage' => 'India heritage culture UNESCO recognition',
    ],

    /*
    | Google News search feed parameters. `hl`/`gl`/`ceid` pin the edition to
    | India so the results are the Indian press rather than the world's.
    */
    'edition' => [
        'hl' => 'en-IN',
        'gl' => 'IN',
        'ceid' => 'IN:en',
    ],

    /*
    | How many headlines to keep after merging and de-duplicating every topic.
    */
    'limit' => 60,

    /*
    | How many headlines one topic may contribute. A busy topic — sport on an
    | Asian Games week, say — would otherwise crowd out the rest of the page.
    */
    'per_topic' => 12,

    /*
    | Headlines older than this are dropped, so a quiet week does not leave the
    | page showing last month's news.
    */
    'max_age_days' => 30,

    /*
    | Cache lifetime. The scheduled command refreshes daily; this is the
    | backstop that re-fetches if the scheduler has not run for a while.
    */
    'ttl_hours' => 20,

];
