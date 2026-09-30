@extends('layouts.site')

@section('title', $query !== '' ? $query.' — Search — Pakka Patriot' : 'Search — Pakka Patriot')
@section('description', 'Search every place, person, idea, tradition, creation, story, product and game on Pakka Patriot.')

@php
    /**
     * Site-wide search results — ported from the React SearchPage.
     *
     * Grouping and ordering (Ideas → Places → People → Culture → Create →
     * Stories → Store → Games) comes from App\Services\SiteSearch.
     */
    // url() treats a second argument as path segments, so build the query by hand.
    $searchUrl = fn (string $q = ''): string => $q === '' ? url('/search') : url('/search').'?q='.rawurlencode($q);

    $browseLinks = [
        ['label' => 'Ideas', 'url' => url('/ideas')],
        ['label' => 'Places', 'url' => url('/places')],
        ['label' => 'People', 'url' => url('/people')],
        ['label' => 'Culture', 'url' => url('/culture')],
        ['label' => 'Create', 'url' => url('/create')],
        ['label' => 'Stories', 'url' => route('shop.blog.index')],
        ['label' => 'Games', 'url' => url('/play')],
    ];
@endphp

@section('content')
<div class="bg-brand-cream min-h-screen">

    {{-- HERO + SEARCH BOX --}}
    <section class="relative bg-[#0A2240] text-white overflow-hidden">
        <div class="absolute inset-0 opacity-[0.06] pointer-events-none select-none" aria-hidden="true">
            <div class="absolute top-8 left-8 w-40 h-40 border-t-4 border-r-4 border-white rounded-tr-full"></div>
            <div class="absolute bottom-6 right-10 w-56 h-56 border-b-4 border-l-4 border-white rounded-bl-full"></div>
            <div class="absolute top-1/3 right-1/4 w-24 h-24 border border-white rounded-full"></div>
        </div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 text-center">
            <span class="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-black tracking-[0.25em] uppercase text-[#F6B828] mb-3">
                <x-pp-icon name="search" :size="14" /> Search Pakka Patriot
            </span>
            <h1 class="font-brush text-4xl sm:text-6xl leading-tight tracking-wide">
                What are you <span class="text-[#F6B828]">looking for?</span>
            </h1>

            <form method="GET" action="{{ url('/search') }}" class="mt-8 max-w-2xl mx-auto" autocomplete="off">
                <div class="relative">
                    <x-pp-icon name="search" :size="20" class="absolute left-5 top-1/2 -translate-y-1/2 text-[#8A9EB4]" />
                    <input autofocus type="text" name="q" value="{{ $query }}" id="siteSearchPageInput"
                           placeholder="Try “Taj Mahal”, “zero”, “Bhagat Singh”, “chess”, “shampoo”…"
                           aria-label="Search the whole site"
                           class="w-full bg-white text-[#0A2240] pl-12 sm:pl-14 pr-12 py-4 rounded-2xl text-sm sm:text-base font-semibold shadow-xl focus:outline-none focus:ring-4 focus:ring-[#F6B828]/40 border border-transparent">
                    @if($query !== '')
                        <a href="{{ url('/search') }}"
                           class="absolute right-4 top-1/2 -translate-y-1/2 text-[#8A9EB4] hover:text-[#F6B828] p-1 rounded-full transition-colors"
                           aria-label="Clear search">
                            <x-pp-icon name="x" :size="18" />
                        </a>
                    @endif
                </div>
                <p class="mt-3 text-xs text-[#8FB2D6] font-semibold">
                    @if($query !== '')
                        Found <span class="text-[#F6B828] font-black">{{ $total }}</span> {{ $total === 1 ? 'result' : 'results' }} across the whole site
                    @else
                        Search ideas, places, people, culture, creations, stories, merch &amp; games
                    @endif
                </p>
            </form>
        </div>
    </section>

    {{-- RESULTS --}}
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        @if($query === '')
            <div class="text-center py-16">
                <div class="w-16 h-16 rounded-2xl bg-[#FAF6EC] border border-[#E4DCB9] flex items-center justify-center mx-auto mb-4">
                    <x-pp-icon name="search" :size="28" class="text-[#C8C5B9]" />
                </div>
                <h2 class="font-display font-black text-xl text-[#0A2240]">Type to search the whole site</h2>
                <p class="text-sm text-[#4E637A] font-medium mt-1 max-w-md mx-auto">
                    Every place, person, idea, tradition, creation, story, product and game on Pakka Patriot — in one box.
                </p>
                <div class="flex flex-wrap justify-center gap-2 mt-6">
                    @foreach($suggestions as $term)
                        <a href="{{ $searchUrl($term) }}"
                           class="text-xs font-bold text-[#0A2240] bg-white border border-[#E4DCB9] hover:border-[#F6B828] hover:bg-[#FEF5E0] px-4 py-2 rounded-full transition-all">
                            {{ $term }}
                        </a>
                    @endforeach
                </div>
            </div>
        @elseif($total === 0)
            <div class="text-center py-16">
                <div class="w-16 h-16 rounded-2xl bg-red-50 border border-red-100 flex items-center justify-center mx-auto mb-4">
                    <x-pp-icon name="search" :size="28" class="text-red-300" />
                </div>
                <h2 class="font-display font-black text-xl text-[#0A2240]">No matches for “{{ $query }}”</h2>
                <p class="text-sm text-[#4E637A] font-medium mt-1 max-w-md mx-auto">
                    Try a shorter word, a different spelling, or browse one of the collections below.
                </p>
                <div class="flex flex-wrap justify-center gap-2 mt-6">
                    @foreach($browseLinks as $link)
                        <a href="{{ $link['url'] }}"
                           class="text-xs font-bold text-[#0A2240] bg-white border border-[#E4DCB9] hover:border-[#F6B828] hover:bg-[#FEF5E0] px-4 py-2 rounded-full transition-all">
                            Browse {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            <div class="space-y-10">
                @foreach($groups as $group)
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="w-8 h-8 rounded-lg bg-[#0A2240] text-[#F6B828] flex items-center justify-center">
                                <x-pp-icon :name="$group['icon']" :size="16" />
                            </span>
                            <h2 class="font-display font-black text-lg text-[#0A2240] uppercase tracking-wide">{{ $group['label'] }}</h2>
                            <span class="text-[11px] font-black text-[#587760] bg-[#EAF1EB] px-2.5 py-1 rounded-full">
                                {{ $group['count'] }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($group['items'] as $match)
                                @php
                                    // Store results open the product modal; every other
                                    // kind links to its own page.
                                    $product = $match['product'] ?? null;
                                    $cardClass = 'group bg-white rounded-2xl border border-[#F0EBE0] hover:border-[#F6B828]/40 shadow-sm hover:shadow-lg transition-all duration-300 p-4 flex gap-4 text-left';
                                @endphp

                                @if($product)
                                    <div class="{{ $cardClass }} cursor-pointer"
                                         wire:click="$dispatch('open-product', { id: {{ $product['id'] }} })">
                                        <x-pp-search-result :match="$match" :product="$product" :icon="$group['icon']" />
                                    </div>
                                @else
                                    <a href="{{ $match['url'] }}" class="{{ $cardClass }}">
                                        <x-pp-search-result :match="$match" :icon="$group['icon']" />
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{-- Footer nav --}}
                <div class="flex flex-wrap items-center justify-center gap-2 pt-4 border-t border-[#F0EBE0]">
                    <span class="text-xs font-bold text-[#8A9EB4] uppercase tracking-widest">Didn't find it? Browse</span>
                    @foreach([['Ideas', url('/ideas')], ['Places', url('/places')], ['People', url('/people')], ['Culture', url('/culture')], ['Create', url('/create')], ['Stories', route('shop.blog.index')], ['Games', url('/play')], ['Store', url('/shop')]] as [$label, $href])
                        <a href="{{ $href }}"
                           class="inline-flex items-center gap-1 text-xs font-bold text-[#0A2240] bg-white border border-[#E4DCB9] hover:border-[#F6B828] hover:bg-[#FEF5E0] px-3.5 py-1.5 rounded-full transition-all">
                            {{ $label }}
                            <x-pp-icon name="arrow-right" :size="12" />
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</div>
@endsection
