@php
    /**
     * Header chrome — ported from the React Header component.
     */

    // Love categories: each opens its own page.
    // Nav icons are sliced from design/navigation.png into public/images/nav/.
    $loveCategories = [
        ['id' => 'PEOPLE', 'label' => 'PEOPLE', 'icon' => 'users', 'image' => 'images/nav/people.png', 'href' => route('collection.browse', 'people')],
        ['id' => 'IDEAS', 'label' => 'IDEAS', 'icon' => 'lightbulb', 'image' => 'images/nav/ideas.png', 'href' => route('collection.browse', 'ideas')],
        ['id' => 'PLACES', 'label' => 'PLACES', 'icon' => 'map-pin', 'image' => 'images/nav/places.png', 'href' => route('collection.browse', 'places')],
        ['id' => 'CULTURE', 'label' => 'CULTURE', 'icon' => 'palette', 'image' => 'images/nav/culture.png', 'href' => route('collection.browse', 'culture')],
        // CREATE opens the maker's space (React's /create), not the Creations collection.
        ['id' => 'CREATE', 'label' => 'CREATE', 'icon' => 'sparkles', 'image' => 'images/nav/create.png', 'href' => url('/create')],
        ['id' => 'STORIES', 'label' => 'STORIES', 'icon' => 'newspaper', 'image' => 'images/nav/stories.png', 'href' => route('shop.blog.index')],
        ['id' => 'GAMES', 'label' => 'GAMES', 'icon' => 'gamepad-2', 'image' => 'images/nav/games.png', 'href' => url('/play')],
    ];

    // Secondary navigation — icons sliced from design/secondary.jpg.
    // Apps, For Teachers and Resources are holding pages for now; the sections
    // they used to link to are still reachable from the footer.
    $secondaryNav = [
        ['label' => 'Merch', 'image' => 'images/secondary-nav/icon-1.png', 'href' => route('shop')],
        ['label' => 'News', 'image' => 'images/secondary-nav/icon-2.png', 'href' => route('news')],
        ['label' => 'Panchangam', 'image' => 'images/secondary-nav/icon-3.png', 'href' => route('panchangam')],
        ['label' => 'Apps', 'image' => 'images/secondary-nav/icon-4.png', 'href' => route('coming-soon.apps')],
        ['label' => 'Downloads', 'image' => 'images/secondary-nav/icon-5.png', 'href' => route('downloads')],
        ['label' => 'For Teachers', 'image' => 'images/secondary-nav/icon-6.png', 'href' => route('coming-soon.teachers')],
        ['label' => 'Resources', 'image' => 'images/secondary-nav/icon-7.png', 'href' => route('coming-soon.resources')],
        ['label' => 'About', 'image' => 'images/secondary-nav/icon-8.png', 'href' => route('about')],
    ];
@endphp

<header class="sticky top-0 z-40 w-full bg-[#FCFAF5]/95 backdrop-blur-md border-b border-[#F0EBE0] shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex-shrink-0 flex items-center">
                <img src="{{ asset('images/logo-dark.png') }}" alt="Pakka Patriot" class="h-12 w-auto object-contain">
            </a>

            {{-- Desktop love categories --}}
            <nav class="hidden lg:flex items-center gap-1 xl:gap-2">
                @foreach($loveCategories as $cat)
                    @php $isActive = request()->is(ltrim(parse_url($cat['href'], PHP_URL_PATH), '/').'*'); @endphp
                    <a href="{{ $cat['href'] }}"
                       class="group flex items-center gap-1.5 px-2.5 py-2 rounded-md text-sm font-semibold transition-all duration-200 relative
                              {{ $isActive ? 'text-[#F6B828]' : 'text-[#0A2240] hover:text-[#F6B828] hover:bg-[#F8F4EA]' }}">
                        <img src="{{ asset($cat['image']) }}" alt="" aria-hidden="true"
                             class="h-5 w-5 xl:h-6 xl:w-6 object-contain flex-shrink-0 transition-transform duration-200 {{ $isActive ? 'scale-110' : 'group-hover:scale-110' }}">
                        {{ $cat['label'] }}
                        @if($isActive)
                            <span class="absolute bottom-0 left-3 right-3 h-0.5 bg-[#F6B828] rounded-full"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- Actions: search, join journey, mobile toggle --}}
            <div class="flex items-center gap-2 sm:gap-4">

                <livewire:site-search-box />

                <button type="button" data-open-journey
                        class="hidden sm:block bg-[#F6B828] hover:bg-[#DAA520] text-white px-5 py-2.5 rounded-full text-sm font-bold shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                    Join the Journey
                </button>

                <button type="button" id="mobileMenuToggle"
                        class="lg:hidden p-2 text-[#0A2240] hover:text-[#F6B828] transition-colors"
                        aria-label="Toggle menu" aria-expanded="false">
                    <x-pp-icon name="menu" id="mobileMenuIcon" :size="24" />
                    <x-pp-icon name="x" id="mobileMenuCloseIcon" :size="24" class="hidden" />
                </button>
            </div>
        </div>
    </div>

    {{-- Secondary navigation --}}
    <div class="bg-[#122A44] text-white py-2 sm:py-3 px-4 shadow-inner overflow-x-auto scrollbar-none border-t border-[#1F3D5E]">
        <div class="max-w-7xl mx-auto flex items-center justify-start md:justify-center gap-3 sm:gap-6 min-w-max">
            @foreach($secondaryNav as $item)
                @php $isActive = request()->is(ltrim(parse_url($item['href'], PHP_URL_PATH), '/').'*'); @endphp
                <a href="{{ $item['href'] }}"
                   class="group flex items-center gap-2 px-3 py-1.5 rounded-full text-[11px] sm:text-xs font-bold tracking-wide uppercase transition-all duration-200
                          {{ $isActive ? 'bg-[#F6B828] text-white shadow-md scale-105' : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                    <img src="{{ asset($item['image']) }}" alt="" aria-hidden="true"
                         class="h-6 w-6 sm:h-7 sm:w-7 object-contain flex-shrink-0 transition-transform duration-200 group-hover:scale-110">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Mobile menu panel --}}
    <div id="mobileMenuPanel" class="lg:hidden hidden bg-[#FCFAF5] border-t border-[#F0EBE0] px-4 pt-2 pb-6 space-y-2 shadow-lg">
        @foreach($loveCategories as $cat)
            @php $isActive = request()->is(ltrim(parse_url($cat['href'], PHP_URL_PATH), '/').'*'); @endphp
            <a href="{{ $cat['href'] }}"
               class="flex items-center gap-3 w-full text-left px-4 py-2.5 rounded-lg text-sm font-bold tracking-wide transition-all
                      {{ $isActive ? 'bg-[#FEF5E0] text-[#F6B828]' : 'text-[#0A2240] hover:bg-[#FAF6EC]' }}">
                <img src="{{ asset($cat['image']) }}" alt="" aria-hidden="true" class="h-6 w-6 object-contain flex-shrink-0">
                {{ $cat['label'] }}
            </a>
        @endforeach
        <div class="pt-4 border-t border-[#E4DCB9]">
            <button type="button" data-open-journey
                    onclick="document.getElementById('mobileMenuPanel')?.classList.add('hidden')"
                    class="w-full text-center bg-[#F6B828] text-white py-3 rounded-xl font-bold hover:bg-[#DAA520] transition-all">
                Join the Journey
            </button>
        </div>
    </div>
</header>
