<?php

namespace App\Data;

/**
 * The maker-activity cards on /create.
 *
 * Ported from the React CreatePage's ACTIVITIES array. The long-form content
 * for each activity (tagline, what it is, what Bhārat is known for, try-this)
 * lives in the `ppcreate_activities` table and drives the detail pages; these
 * are the card-level labels, colours and calls to action.
 */
class CreateCards
{
    /**
     * @return array<int, array<string, string>>
     */
    public static function all(): array
    {
        return [
            [
                'slug' => 'draw',
                'id' => '01',
                'title' => 'DRAW',
                'emoji' => '🎨',
                'tile' => 'from-[#F3E8FF] to-[#E9D5FF]',
                'ring' => 'bg-violet-100 text-violet-700',
                'description' => 'Learn step-by-step drawing lessons and colouring sheets.',
                'cta' => 'Start Drawing',
                'button' => 'bg-[#7C3AED] hover:bg-[#6D28D9]',
            ],
            [
                'slug' => 'make',
                'id' => '02',
                'title' => 'MAKE',
                'emoji' => '✂️',
                'tile' => 'from-[#FFEDD5] to-[#FED7AA]',
                'ring' => 'bg-orange-100 text-orange-700',
                'description' => "Fun crafts inspired by Bhārat's culture, festivals and traditions.",
                'cta' => 'Start Making',
                'button' => 'bg-[#F97316] hover:bg-[#EA580C]',
            ],
            [
                'slug' => 'experiment',
                'id' => '03',
                'title' => 'EXPERIMENT',
                'emoji' => '🧪',
                'tile' => 'from-[#DCFCE7] to-[#BBF7D0]',
                'ring' => 'bg-green-100 text-green-700',
                'description' => 'Simple science experiments you can try at home.',
                'cta' => 'Start Experimenting',
                'button' => 'bg-[#16A34A] hover:bg-[#15803D]',
            ],
            [
                'slug' => 'build',
                'id' => '04',
                'title' => 'BUILD',
                'emoji' => '🏗️',
                'tile' => 'from-[#DBEAFE] to-[#BFDBFE]',
                'ring' => 'bg-blue-100 text-blue-700',
                'description' => 'Build models, machines and structures. Think like an engineer!',
                'cta' => 'Start Building',
                'button' => 'bg-[#2563EB] hover:bg-[#1D4ED8]',
            ],
            [
                'slug' => 'write',
                'id' => '05',
                'title' => 'WRITE',
                'emoji' => '✍️',
                'tile' => 'from-[#FCE7F3] to-[#FBCFE8]',
                'ring' => 'bg-pink-100 text-pink-700',
                'description' => 'Stories, poems, comics and more. Let your imagination shine.',
                'cta' => 'Start Writing',
                'button' => 'bg-[#DB2777] hover:bg-[#BE185D]',
            ],
            [
                'slug' => 'newspaper',
                'id' => '06',
                'title' => 'MAKE YOUR OWN NEWSPAPER',
                'emoji' => '📰',
                'tile' => 'from-[#DCFCE7] to-[#BBF7D0]',
                'ring' => 'bg-emerald-100 text-emerald-700',
                'description' => 'Create and design your very own newspaper.',
                'cta' => 'Make Newspaper',
                'button' => 'bg-[#16A34A] hover:bg-[#15803D]',
            ],
            [
                'slug' => 'videos',
                'id' => '07',
                'title' => 'CREATE VIDEOS',
                'emoji' => '🎬',
                'tile' => 'from-[#FFEDD5] to-[#FED7AA]',
                'ring' => 'bg-amber-100 text-amber-700',
                'description' => 'Make 60-second videos and share your stories, ideas and talents.',
                'cta' => 'Start Creating',
                'button' => 'bg-[#F97316] hover:bg-[#EA580C]',
            ],
            [
                'slug' => 'my-bharat',
                'id' => '08',
                'title' => 'MY INDIA',
                'emoji' => '📸',
                'tile' => 'from-[#F3E8FF] to-[#E9D5FF]',
                'ring' => 'bg-violet-100 text-violet-700',
                'description' => 'Upload photos of your place and show the beauty around you.',
                'cta' => 'Explore My Bhārat',
                'button' => 'bg-[#7C3AED] hover:bg-[#6D28D9]',
            ],
            [
                'slug' => 'make-a-game',
                'id' => '09',
                'title' => 'MAKE A GAME',
                'emoji' => '🎲',
                'tile' => 'from-[#DBEAFE] to-[#BFDBFE]',
                'ring' => 'bg-sky-100 text-sky-700',
                'description' => 'Design your own board game. Choose theme, rules and challenges.',
                'cta' => 'Create Game',
                'button' => 'bg-[#2563EB] hover:bg-[#1D4ED8]',
            ],
            [
                'slug' => 'puzzles',
                'id' => '10',
                'title' => 'PUZZLES & PRINTABLES',
                'emoji' => '🧩',
                'tile' => 'from-[#EDE9FE] to-[#DDD6FE]',
                'ring' => 'bg-indigo-100 text-indigo-700',
                'description' => 'Puzzles, crosswords, mazes and more. Download & print.',
                'cta' => 'Explore Puzzles',
                'button' => 'bg-[#7C3AED] hover:bg-[#6D28D9]',
            ],
            [
                'slug' => 'grandparents',
                'id' => '11',
                'title' => 'CREATE WITH GRANDPARENTS',
                'emoji' => '👵🏽',
                'tile' => 'from-[#FFF7ED] to-[#FED7AA]',
                'ring' => 'bg-orange-100 text-orange-700',
                'description' => 'Ask, record and preserve stories from your grandparents.',
                'cta' => 'Start Recording',
                'button' => 'bg-[#F97316] hover:bg-[#EA580C]',
            ],
            [
                'slug' => 'create-for-bharat',
                'id' => '12',
                'title' => 'CREATE FOR INDIA',
                'emoji' => '💡',
                'tile' => 'from-[#DCFCE7] to-[#BBF7D0]',
                'ring' => 'bg-green-100 text-green-700',
                'description' => 'Take challenges and create ideas that make Bhārat better.',
                'cta' => 'Accept Challenge',
                'button' => 'bg-[#16A34A] hover:bg-[#15803D]',
            ],
        ];
    }

    /**
     * The maker's process strip under the hero.
     *
     * @return array<int, array<string, string>>
     */
    public static function processSteps(): array
    {
        return [
            ['icon' => 'book-open', 'label' => 'Learn it', 'colour' => 'bg-[#0D9488] text-white'],
            ['icon' => 'lightbulb', 'label' => 'Imagine it', 'colour' => 'bg-[#F59E0B] text-white'],
            ['icon' => 'pencil', 'label' => 'Create it', 'colour' => 'bg-[#E11D48] text-white'],
            ['icon' => 'users', 'label' => 'Share it', 'colour' => 'bg-[#6366F1] text-white'],
        ];
    }

    /**
     * Colours of the six letters in the "CREATE" hero wordmark.
     *
     * @return array<int, array{ch: string, colour: string}>
     */
    public static function heroLetters(): array
    {
        return [
            ['ch' => 'C', 'colour' => '#F97316'],
            ['ch' => 'R', 'colour' => '#F59E0B'],
            ['ch' => 'E', 'colour' => '#14B8A6'],
            ['ch' => 'A', 'colour' => '#EF4444'],
            ['ch' => 'T', 'colour' => '#F97316'],
            ['ch' => 'E', 'colour' => '#0D9488'],
        ];
    }

    /**
     * Gallery strip ("Made by Pakka Patriots").
     *
     * @return array<int, array<string, mixed>>
     */
    public static function creations(): array
    {
        return [
            ['emoji' => '🦚', 'title' => 'Peacock', 'by' => 'Ananya', 'city' => 'Bengaluru', 'tile' => 'from-[#F3E8FF] to-[#E9D5FF]'],
            ['emoji' => '🏰', 'title' => 'My Fort', 'by' => 'Vihaan', 'city' => 'Jaipur', 'tile' => 'from-[#FFEDD5] to-[#FED7AA]'],
            ['emoji' => '🎭', 'title' => 'Festival in My City', 'by' => 'Kavya', 'city' => 'Pune', 'tile' => 'from-[#DCFCE7] to-[#BBF7D0]', 'video' => true],
        ];
    }

    /**
     * Printable downloads strip.
     *
     * @return array<int, array<string, string>>
     */
    public static function printables(): array
    {
        return [
            ['emoji' => '🕌', 'title' => 'Hampi Colouring Sheet'],
            ['emoji' => '🗺️', 'title' => 'Bhārat Map Puzzle'],
            ['emoji' => '🦁', 'title' => 'Animals of Bhārat Colouring Pages'],
        ];
    }
}
