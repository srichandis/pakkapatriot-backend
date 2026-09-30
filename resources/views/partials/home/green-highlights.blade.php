@php
    // Pakka character pillars → the pages they open.
    $highlights = [
        ['id' => 'learns', 'title' => 'LEARNS', 'subtitle' => 'about timeless wisdom.', 'image' => 'images/1sec.png', 'href' => route('collection.browse', 'ideas')],
        ['id' => 'explores', 'title' => 'EXPLORES', 'subtitle' => 'incredible places and culture.', 'image' => 'images/2sec.png', 'href' => route('collection.browse', 'places')],
        ['id' => 'celebrates', 'title' => 'CELEBRATES', 'subtitle' => 'our traditions and festivals.', 'image' => 'images/3sec.png', 'href' => route('collection.browse', 'culture')],
        ['id' => 'creates', 'title' => 'CREATES', 'subtitle' => 'a better tomorrow with ideas.', 'image' => 'images/4sec.png', 'href' => url('/create')],
    ];
@endphp

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-[#688059] rounded-3xl shadow-lg text-white p-6 sm:p-8 md:p-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-4 lg:divide-x lg:divide-white/20">
            @foreach($highlights as $card)
                <a href="{{ $card['href'] }}"
                   class="flex items-center gap-4 px-2 sm:px-4 cursor-pointer group hover:bg-white/5 py-3 rounded-2xl transition-all duration-200">
                    <div class="flex-shrink-0 transform group-hover:scale-105 transition-transform duration-200">
                        <img src="{{ asset($card['image']) }}" alt="{{ $card['title'] }}"
                             class="max-w-[120px] sm:max-w-[160px] h-auto object-contain" referrerpolicy="no-referrer">
                    </div>

                    <div class="flex flex-col text-left">
                        <span class="font-display font-black text-lg tracking-wider text-white flex items-center gap-1 group-hover:text-brand-yellow transition-colors">
                            {{ $card['title'] }}
                        </span>
                        <span class="text-xs sm:text-sm text-brand-cream/80 font-medium leading-tight">
                            {{ $card['subtitle'] }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
