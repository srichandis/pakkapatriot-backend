@php
    /*
     * "What You'll Find on PakkaPatriot" — the six cards from design/cards.jpg.
     * Illustrations are sliced to public/images/cards/; each card opens the page
     * its caption points at.
     */
    $cards = [
        ['title' => 'Amazing Stories', 'text' => 'Real people, forgotten history, festivals, and incredible places.', 'image' => 'images/cards/stories.png', 'bg' => '#FCF7EC', 'href' => route('shop.blog.index')],
        ['title' => 'Explore Places', 'text' => 'Temples, forts, cities, nature spots and hidden gems.', 'image' => 'images/cards/places.png', 'bg' => '#F2F7F0', 'href' => route('collection.browse', 'places')],
        ['title' => 'Indian Culture', 'text' => 'Festivals, traditions, food, art, language and everyday life.', 'image' => 'images/cards/culture.png', 'bg' => '#FCF1F4', 'href' => route('collection.browse', 'culture')],
        ['title' => 'Curious Questions', 'text' => 'Simple answers to big questions about India and the world.', 'image' => 'images/cards/questions.png', 'bg' => '#FCF7ED', 'href' => route('collection.browse', 'ideas')],
        ['title' => 'Create & Make', 'text' => 'DIY crafts, activities, art and fun projects for all ages.', 'image' => 'images/cards/create.png', 'bg' => '#F2F6EE', 'href' => url('/create')],
        ['title' => 'Fun Zone', 'text' => 'Quizzes, puzzles, brain teasers and challenges.', 'image' => 'images/cards/fun-zone.png', 'bg' => '#FCF6EC', 'href' => url('/play')],
    ];
@endphp

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
    <div class="text-center mb-10 select-none">
        <h2 class="font-brush text-3xl sm:text-4xl lg:text-5xl text-[#0A2240] tracking-wide">
            What You'll Find on <span class="text-[#F6B828]">PakkaPatriot</span>
        </h2>
    </div>

    {{-- Six across, always one row — scrolls sideways on narrow screens. --}}
    <div class="overflow-x-auto pb-2">
        <div class="grid grid-cols-6 gap-3 sm:gap-4 min-w-[760px]">
            @foreach($cards as $card)
                <a href="{{ $card['href'] }}" style="background-color: {{ $card['bg'] }}"
                   class="group flex flex-col items-center text-center p-4 sm:p-5 rounded-2xl cursor-pointer border border-black/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <img src="{{ asset($card['image']) }}" alt="{{ $card['title'] }}"
                         class="h-20 w-auto object-contain mb-4 transition-transform duration-300 group-hover:scale-110"
                         referrerpolicy="no-referrer">

                    <h3 class="font-display font-black text-sm sm:text-base text-[#0A2240] tracking-tight mb-2">
                        {{ $card['title'] }}
                    </h3>

                    <p class="text-xs text-[#2F445A]/80 font-medium leading-snug">
                        {{ $card['text'] }}
                    </p>
                </a>
            @endforeach
        </div>
    </div>
</section>
