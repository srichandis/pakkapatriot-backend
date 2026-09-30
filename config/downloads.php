<?php

/*
|--------------------------------------------------------------------------
| Downloads
|--------------------------------------------------------------------------
|
| The printable / downloadable library, grouped into the eleven material
| types the site offers. Each entry here becomes a page at
| /downloads/{slug} listing its items.
|
| Items carry no file of their own in this catalogue: a file's expected path
| is derived from the category slug, the item title and the format —
| public/downloads/{category}/{item-slug}.{pdf|png|apk|zip}. An item whose
| file has not been dropped in yet is shown as "Coming soon" rather than as
| a dead link, so the pages stay honest as the library fills up.
|
*/

return [

    'categories' => [

        'printable-worksheets' => [
            'emoji' => '📄',
            'title' => 'Printable Worksheets',
            'blurb' => 'Fill-in-the-blank practice on Bhārat\'s history, geography and culture — print a set for the class or the kitchen table.',
            'accent' => '#2563EB',
            'items' => [
                ['title' => 'States & Capitals Worksheet', 'description' => 'Match every state with its capital, with a blank map to fill in.', 'format' => 'pdf', 'meta' => '2 pages'],
                ['title' => 'Ancient Bhārat Timeline Worksheet', 'description' => 'Place the Indus Valley, the Vedas, the Mauryas and the Guptas in order.', 'format' => 'pdf', 'meta' => '3 pages'],
                ['title' => 'National Symbols Worksheet', 'description' => 'Flag, emblem, anthem, bird, animal and tree — name them all.', 'format' => 'pdf', 'meta' => '2 pages'],
                ['title' => 'Famous Bhāratīya Scientists Worksheet', 'description' => 'Aryabhata to Ramanujan: match the mind to the discovery.', 'format' => 'pdf', 'meta' => '2 pages'],
            ],
        ],

        'activity-sheets' => [
            'emoji' => '🧩',
            'title' => 'Activity Sheets',
            'blurb' => 'Crosswords, word searches and puzzles that keep young minds busy while they pick up the facts.',
            'accent' => '#7C3AED',
            'items' => [
                ['title' => 'Monuments Match-the-Pairs', 'description' => 'Pair each monument with the city and the century it was built in.', 'format' => 'pdf', 'meta' => '2 pages'],
                ['title' => 'Festival Word Search', 'description' => 'Twenty festivals hidden in a grid — Diwali to Onam.', 'format' => 'pdf', 'meta' => '1 page'],
                ['title' => 'Freedom Fighters Crossword', 'description' => 'Clues drawn from the freedom struggle, 1857 to 1947.', 'format' => 'pdf', 'meta' => '2 pages'],
                ['title' => 'Map the Rivers Puzzle', 'description' => 'Trace the Ganga, Yamuna, Narmada, Godavari and Kaveri.', 'format' => 'pdf', 'meta' => '2 pages'],
            ],
        ],

        'colouring-pages' => [
            'emoji' => '🎨',
            'title' => 'Colouring Pages',
            'blurb' => 'Line art of the things Bhārat is loved for — ready to colour with crayons, pencils or paints.',
            'accent' => '#DB2777',
            'items' => [
                ['title' => 'Diya & Rangoli Colouring Page', 'description' => 'A lamp-lit rangoli for Diwali, with space to add your own pattern.', 'format' => 'pdf', 'meta' => '1 page'],
                ['title' => 'Taj Mahal & Hawa Mahal', 'description' => 'Two of the country\'s best-loved silhouettes on one sheet.', 'format' => 'pdf', 'meta' => '2 pages'],
                ['title' => 'Peacock & National Symbols', 'description' => 'The national bird among lotus, banyan and chakra motifs.', 'format' => 'pdf', 'meta' => '2 pages'],
                ['title' => 'Kathakali Faces', 'description' => 'The painted faces of Kerala\'s dance-drama, ready to fill in.', 'format' => 'pdf', 'meta' => '2 pages'],
            ],
        ],

        'travel-guides' => [
            'emoji' => '🗺️',
            'title' => 'Travel & Activity Guides',
            'blurb' => 'Short, printable guides for travelling families — what to see, what to eat, and what to ask along the way.',
            'accent' => '#0D9488',
            'items' => [
                ['title' => 'A Weekend in Hampi', 'description' => 'Two days among the boulders and ruins, with a route map.', 'format' => 'pdf', 'meta' => '6 pages'],
                ['title' => 'Jaipur With Children', 'description' => 'Forts, puppet shows and sweet shops, paced for small legs.', 'format' => 'pdf', 'meta' => '5 pages'],
                ['title' => 'Kerala Backwaters Guide', 'description' => 'Houseboats, toddy shops and the quietest stretches of the canals.', 'format' => 'pdf', 'meta' => '5 pages'],
                ['title' => 'Himalayan Temple Trail', 'description' => 'Kedarnath to Tungnath, with altitude and season notes.', 'format' => 'pdf', 'meta' => '7 pages'],
            ],
        ],

        'educational-pdfs' => [
            'emoji' => '📚',
            'title' => 'Educational PDFs',
            'blurb' => 'Longer illustrated reads on the ideas, inventions and institutions this land gave the world.',
            'accent' => '#0A2240',
            'items' => [
                ['title' => 'The Story of Zero', 'description' => 'How a placeholder became a number, and changed mathematics.', 'format' => 'pdf', 'meta' => '12 pages'],
                ['title' => 'Ayurveda: A Beginner\'s Primer', 'description' => 'The three doshas, the daily rhythm, and the six tastes.', 'format' => 'pdf', 'meta' => '10 pages'],
                ['title' => 'Bhārat\'s Space Journey', 'description' => 'From sounding rockets to the Moon and Mars, told in pictures.', 'format' => 'pdf', 'meta' => '14 pages'],
                ['title' => 'Sanskrit Loanwords in English', 'description' => 'The words that travelled west and stayed — with a full list.', 'format' => 'pdf', 'meta' => '8 pages'],
            ],
        ],

        'shloka-sheets' => [
            'emoji' => '🕉️',
            'title' => 'Shloka & Subhashita Sheets',
            'blurb' => 'Verse sheets with transliteration, meaning and pronunciation, sized for a wall or a binder.',
            'accent' => '#EA580C',
            'items' => [
                ['title' => 'Everyday Shlokas with Meaning', 'description' => 'Morning, meal and bedtime verses with word-by-word glosses.', 'format' => 'pdf', 'meta' => '4 pages'],
                ['title' => 'Subhashitas of Chanakya', 'description' => 'Twenty-four wise sayings with plain-language explanations.', 'format' => 'pdf', 'meta' => '6 pages'],
                ['title' => 'Gayatri Mantra & Sandhya', 'description' => 'The mantra, its metre, and the three daily offerings.', 'format' => 'pdf', 'meta' => '3 pages'],
                ['title' => 'Vedanta in Ten Shlokas', 'description' => 'Ten verses that carry the heart of the Upanishads.', 'format' => 'pdf', 'meta' => '5 pages'],
            ],
        ],

        'surya-namaskar' => [
            'emoji' => '☀️',
            'title' => 'Surya Namaskar Guides',
            'blurb' => 'Step-by-step sequences with breath cues, for the mat or the classroom floor.',
            'accent' => '#D97706',
            'items' => [
                ['title' => 'Twelve Steps, Illustrated', 'description' => 'Every posture in the classic sequence, with breath marks.', 'format' => 'pdf', 'meta' => '4 pages'],
                ['title' => 'Surya Namaskar & Breath', 'description' => 'Pairing movement with breath, and the Sanskrit count.', 'format' => 'pdf', 'meta' => '3 pages'],
                ['title' => 'Family Morning Routine', 'description' => 'A ten-minute household routine anyone can start.', 'format' => 'pdf', 'meta' => '2 pages'],
                ['title' => 'A Beginner\'s Four-Week Plan', 'description' => 'Three rounds a week, building to twelve a morning.', 'format' => 'pdf', 'meta' => '6 pages'],
            ],
        ],

        'panchangam-calendars' => [
            'emoji' => '📅',
            'title' => 'Panchangam Calendars',
            'blurb' => 'Printable almanac pages — month views, festival lists and tithi charts to pin above the desk.',
            'accent' => '#0369A1',
            'items' => [
                ['title' => 'Monthly Panchangam Wall Chart', 'description' => 'Tithi, nakshatra, sunrise and sunset for every day.', 'format' => 'pdf', 'meta' => '1 page'],
                ['title' => 'Festival Calendar of the Year', 'description' => 'Every major festival with the tithi it falls on.', 'format' => 'pdf', 'meta' => '4 pages'],
                ['title' => 'Tithi & Nakshatra Wall Chart', 'description' => 'A year of tithis at a glance, A3 print-ready.', 'format' => 'pdf', 'meta' => 'A3'],
                ['title' => 'Ekadashi Tracker', 'description' => 'All twenty-four ekadashis with fasting-day notes.', 'format' => 'pdf', 'meta' => '2 pages'],
            ],
        ],

        'game-sheets' => [
            'emoji' => '🎮',
            'title' => 'Printable Game Sheets',
            'blurb' => 'Board-game boards from the courtyards of history, printed and ready for cowrie shells or coins.',
            'accent' => '#15803D',
            'items' => [
                ['title' => 'Pachisi Board', 'description' => 'The classic cross board, two print sizes with rules.', 'format' => 'pdf', 'meta' => 'A3 + A4'],
                ['title' => 'Aadu Puli Aatam Board', 'description' => 'Three tigers against fifteen goats, with the full rules.', 'format' => 'pdf', 'meta' => '1 page'],
                ['title' => 'Chaukabaara Cross Board', 'description' => 'The five-and-seven-house board with castle squares.', 'format' => 'pdf', 'meta' => '1 page'],
                ['title' => 'Chaturvimshati Koṣṭaka Board', 'description' => 'The twenty-four square board and its capture rules.', 'format' => 'pdf', 'meta' => '1 page'],
            ],
        ],

        'wallpapers' => [
            'emoji' => '🖼️',
            'title' => 'Wallpapers',
            'blurb' => 'Bhārat on your lock screen — desktop, tablet and phone sizes of the artwork the site is built on.',
            'accent' => '#9333EA',
            'items' => [
                ['title' => 'Know Bhārat. Be Bhārat. — Desktop', 'description' => 'The masthead type on the navy field, 1920×1080 and 2560×1440.', 'format' => 'png', 'meta' => 'Desktop'],
                ['title' => 'Know Bhārat. Be Bhārat. — Mobile', 'description' => 'A portrait crop with room for the clock, 1170×2532.', 'format' => 'png', 'meta' => 'Mobile'],
                ['title' => 'Monsoon Palaces — Desktop', 'description' => 'Jharokhas and rain clouds in the brand palette.', 'format' => 'png', 'meta' => 'Desktop'],
                ['title' => 'Festival of Lights — Mobile', 'description' => 'A field of diyas for the festive season.', 'format' => 'png', 'meta' => 'Mobile'],
            ],
        ],

        'app-downloads' => [
            'emoji' => '📱',
            'title' => 'App Downloads',
            'blurb' => 'Take the games and stories offline. Installers, store links and packs you can carry without a signal.',
            'accent' => '#4F46E5',
            'items' => [
                ['title' => 'Pakka Patriot for Android', 'description' => 'The full app — stories, games and the daily panchangam.', 'format' => 'apk', 'meta' => 'Android 8+'],
                ['title' => 'Pakka Patriot for iOS', 'description' => 'iPhone and iPad build, available through the App Store.', 'format' => 'apk', 'meta' => 'iOS 15+'],
                ['title' => 'Chaukabaara Board Game App', 'description' => 'Play the cross-board game with family over one screen.', 'format' => 'apk', 'meta' => 'Android 8+'],
                ['title' => 'Offline Story Pack', 'description' => 'Two hundred stories as a single bundle for the road.', 'format' => 'zip', 'meta' => '48 MB'],
            ],
        ],
    ],

];
