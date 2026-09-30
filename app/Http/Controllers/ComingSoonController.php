<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * COMING SOON — For Teachers, Apps and Resources.
 *
 * The three secondary-nav entries are announced but not built yet, so each
 * gets its own holding page with copy about what is coming. They previously
 * pointed at pages that do exist (/explore and /play); those pages are still
 * linked from the footer ("Explore", "Fun Zone") and the header's GAMES entry,
 * so they are deliberately left in place — only the nav links changed.
 */
class ComingSoonController extends Controller
{
    /**
     * The classroom kit.
     */
    public function teachers(): View
    {
        return view('coming-soon', [
            'title' => 'For Teachers',
            'heading' => 'A classroom kit for',
            'accent' => 'Bhārat’s teachers',
            'blurb' => 'Lesson-ready material that brings the country’s stories, science and heritage into the classroom — built with teachers, for teachers.',
            'icon' => 'graduation-cap',
            'eyebrow' => 'For Teachers',
            'highlights' => [
                ['icon' => 'clipboard-check', 'title' => 'Lesson plans', 'text' => 'Ready-to-teach plans mapped to grades and subjects, with objectives and timings.'],
                ['icon' => 'pencil', 'title' => 'Worksheets & activities', 'text' => 'Printable activity sheets, quizzes and class projects on heritage, civics and culture.'],
                ['icon' => 'calendar', 'title' => 'Panchangam for the classroom', 'text' => 'Festivals, tithis and the stories behind them, explained for young learners.'],
                ['icon' => 'users', 'title' => 'Teacher community', 'text' => 'Share what worked, borrow ideas and collaborate with teachers across the country.'],
            ],
            'alsoLive' => [
                ['label' => 'Read the free eBooks', 'href' => route('stories')],
                ['label' => 'Browse printable downloads', 'href' => route('downloads')],
            ],
        ]);
    }

    /**
     * The mobile apps.
     */
    public function apps(): View
    {
        return view('coming-soon', [
            'title' => 'Apps',
            'heading' => 'Pakka Patriot in your',
            'accent' => 'pocket',
            'blurb' => 'The stories, the panchangam and the games you already love on the web — rebuilt for your phone, so Bhārat travels with you.',
            'icon' => 'layout-grid',
            'eyebrow' => 'Apps',
            'highlights' => [
                ['icon' => 'book-open', 'title' => 'Offline reading', 'text' => 'Download stories and eBooks once and read them anywhere, with or without signal.'],
                ['icon' => 'calendar', 'title' => 'Daily panchangam', 'text' => 'Today’s tithi, nakshatram and festivals at a glance, with gentle reminders.'],
                ['icon' => 'gamepad-2', 'title' => 'Games on the go', 'text' => 'The board games and challenges from the Fun Zone, tuned for touch screens.'],
                ['icon' => 'heart', 'title' => 'Saved for later', 'text' => 'Bookmark the people, places and ideas you want to come back to.'],
            ],
            'alsoLive' => [
                ['label' => 'Play in the browser', 'href' => route('play')],
                ['label' => 'Read the daily news', 'href' => route('news')],
            ],
        ]);
    }

    /**
     * The resource library.
     */
    public function resources(): View
    {
        return view('coming-soon', [
            'title' => 'Resources',
            'heading' => 'A resource library for',
            'accent' => 'every patriot',
            'blurb' => 'Everything you need to learn, teach and share Bhārat — catalogued in one place, free to use at home, in class and in the community.',
            'icon' => 'book-open',
            'eyebrow' => 'Resources',
            'highlights' => [
                ['icon' => 'package', 'title' => 'Story packs', 'text' => 'Curated bundles of eBooks and stories grouped by theme, ready to share in one link.'],
                ['icon' => 'palette', 'title' => 'Posters & printables', 'text' => 'Desk posters, colouring pages and wallpapers that celebrate the country.'],
                ['icon' => 'telescope', 'title' => 'Guides & explainers', 'text' => 'Short, sourced explainers on history, science and culture — no jargon.'],
                ['icon' => 'shield', 'title' => 'Community toolkit', 'text' => 'Material for schools, libraries and neighbourhood groups to run their own sessions.'],
            ],
            'alsoLive' => [
                ['label' => 'Browse printable downloads', 'href' => route('downloads')],
                ['label' => 'Explore every story', 'href' => route('explore')],
            ],
        ]);
    }
}
