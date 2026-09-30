@extends('layouts.site')

@section('title', $meta['badgeLabel'].' — Pakka Patriot')
@section('description', $meta['subtitle'])

@php
    /**
     * A collection browse page — Ideas / Places / People / Culture / Create.
     *
     * Ported from the React CollectionBrowsePage. Filtering happens server-side
     * (?q= and ?category=) so every filtered view is a linkable, crawlable URL.
     */
    $browseUrl = \App\Support\Collections::browseUrl($type);
    $total = $items->count();
    $activeSection = $activeCategory ? $sections->firstWhere('id', $activeCategory) : null;
    $results = $filtered ?? collect();
@endphp

@section('content')
<div class="min-h-screen bg-brand-cream relative overflow-hidden">

    {{-- Sticky top bar --}}
    <div class="sticky top-0 z-20 bg-white/90 backdrop-blur-md border-b border-[#F0EBE0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="{{ url('/') }}"
               class="flex items-center gap-2 text-sm font-bold text-[#0A2240] hover:text-[#F6B828] transition-colors">
                <x-pp-icon name="arrow-left" :size="18" />
                Back
            </a>
            <span class="text-[10px] font-black tracking-widest text-[#8A9EB4] uppercase">
                {{ $total }} {{ $meta['itemNoun'] }} to explore
            </span>
        </div>
    </div>

    {{-- Background decor --}}
    <div class="absolute top-20 right-0 w-80 h-80 bg-[#F6B828]/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-40 left-0 w-64 h-64 bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 relative z-10">

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 bg-amber-500/10 rounded-full px-4 py-1.5 mb-4">
                <x-pp-icon :name="$meta['heroIcon']" :size="16" class="text-amber-500" />
                <span class="text-xs font-black tracking-widest text-brand-blue uppercase">{{ $meta['badgeLabel'] }}</span>
            </div>

            <h1 class="font-brush text-5xl sm:text-6xl lg:text-7xl text-brand-blue tracking-wide leading-tight mb-4">
                {{ $meta['titlePrefix'] }} <span class="text-[#F6B828]">{{ $meta['titleHighlight'] }}</span>
            </h1>

            <p class="text-lg sm:text-xl text-[#4E637A] font-medium max-w-3xl mx-auto">
                {{ $meta['subtitle'] }}
            </p>

            <div class="flex flex-wrap justify-center gap-4 mt-6">
                <div class="bg-white rounded-full px-4 py-2 border border-[#F0EBE0] shadow-sm text-sm font-semibold text-brand-blue">
                    ✨ {{ $total }} {{ $meta['itemNoun'] }}
                </div>
                <div class="bg-white rounded-full px-4 py-2 border border-[#F0EBE0] shadow-sm text-sm font-semibold text-brand-blue">
                    🏷️ {{ $sections->count() }} Categories
                </div>
            </div>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ $browseUrl }}" class="mb-8">
            @if($activeCategory)
                <input type="hidden" name="category" value="{{ $activeCategory }}">
            @endif
            <div class="relative max-w-md mx-auto">
                <x-pp-icon name="search" :size="20" class="absolute left-4 top-1/2 -translate-y-1/2 text-[#8A9EB4]" />
                <input type="text" name="q" value="{{ $search }}" autocomplete="off"
                       placeholder="{{ $meta['searchPlaceholder'] }}"
                       class="w-full bg-white border border-[#E4DCB9] rounded-full pl-12 pr-10 py-3 text-sm font-semibold text-brand-blue focus:outline-none focus:border-[#F6B828] focus:ring-2 focus:ring-[#F6B828]/10 transition-all">
                @if($search !== '')
                    <a href="{{ $browseUrl }}{{ $activeCategory ? '?category='.urlencode($activeCategory) : '' }}"
                       class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8A9EB4] hover:text-[#F6B828] transition-colors"
                       aria-label="Clear search">
                        <x-pp-icon name="x" :size="18" />
                    </a>
                @endif
            </div>
        </form>

        {{-- Category chips — click a category to show only its section; ALL shows all --}}
        <div class="flex flex-wrap justify-center gap-3 mb-4">
            <a href="{{ $browseUrl }}{{ $search !== '' ? '?q='.urlencode($search) : '' }}"
               class="px-5 py-2 rounded-full text-xs font-bold tracking-wide transition-all duration-200
                      {{ $activeCategory === '' ? 'bg-brand-blue text-white shadow-md' : 'bg-white border border-[#E4DCB9] text-[#4E637A] hover:border-brand-blue hover:text-brand-blue' }}">
                ALL
            </a>
            @foreach($sections as $section)
                @php
                    $isActive = $activeCategory === $section['id'];
                    $href = $browseUrl.'?category='.urlencode($section['id']).($search !== '' ? '&q='.urlencode($search) : '');
                @endphp
                <a href="{{ $href }}"
                   class="px-5 py-2 rounded-full text-xs font-bold tracking-wide transition-all duration-200
                          {{ $isActive ? 'bg-brand-blue text-white shadow-md' : 'bg-white border border-[#E4DCB9] text-[#4E637A] hover:border-brand-blue hover:text-brand-blue' }}">
                    {{ $section['label'] }} · {{ $section['items']->count() }}
                </a>
            @endforeach
        </div>

        {{-- Results count --}}
        <div class="flex justify-between items-center mb-6 px-1">
            <p class="text-sm font-semibold text-[#8A9EB4]">
                @if($activeSection)
                    <span class="text-brand-blue">
                        {{ $search !== '' ? $results->count() : $activeSection['items']->count() }}
                        {{ ($search !== '' ? $results->count() : $activeSection['items']->count()) === 1 ? $meta['itemNounSingular'] : $meta['itemNoun'] }}
                        in {{ $activeSection['label'] }}
                    </span>
                @elseif($grouped)
                    {{ $total }} {{ $meta['itemNoun'] }} across {{ $sections->count() }} categories
                @else
                    {{ $results->count() }} {{ $results->count() === 1 ? $meta['itemNounSingular'] : $meta['itemNoun'] }} found
                @endif
            </p>
        </div>

        {{-- Results --}}
        @if($grouped && $activeSection)
            {{-- Single category: only this category's section --}}
            <div class="space-y-14">
                @include('collections._section', ['section' => $activeSection, 'type' => $type, 'meta' => $meta])
            </div>
        @elseif($grouped)
            {{-- All sections: cards grouped under category headers. Each section
                 previews the first rows; the full list is on its category URL. --}}
            <div class="space-y-14">
                @foreach($sections as $section)
                    @include('collections._section', ['section' => $section, 'type' => $type, 'meta' => $meta, 'limit' => 9])
                @endforeach
            </div>
        @elseif($results->isEmpty())
            <div class="text-center py-16 bg-white rounded-3xl border border-[#F0EBE0] max-w-md mx-auto">
                <x-pp-icon :name="$meta['heroIcon']" :size="64" class="mx-auto text-[#E4DCB9] mb-4" />
                <h3 class="font-display font-bold text-xl text-brand-blue mb-2">Nothing Found</h3>
                <p class="text-sm text-[#4E637A] font-medium">
                    {{ $search !== '' ? 'Try a different search term.' : 'Try a different category filter.' }}
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($results as $item)
                    <x-pp-collection-card :item="$item" :type="$type" />
                @endforeach
            </div>
        @endif

        {{-- Bottom CTA --}}
        <div class="mt-16 text-center">
            <div class="bg-gradient-to-br from-brand-blue to-[#1A3A5C] rounded-3xl p-8 sm:p-10 shadow-xl relative overflow-hidden">
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-[#F6B828]/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative z-10 space-y-4">
                    <h2 class="font-brush text-3xl sm:text-4xl text-white tracking-wide">
                        One land. <span class="text-[#F6B828]">Endless wonders.</span>
                    </h2>
                    <p class="text-[#B5CADF] font-semibold text-sm max-w-md mx-auto">
                        Which story will you explore next? Every card below began on soil of Bhārat.
                    </p>
                    <div class="flex flex-wrap justify-center gap-2 pt-2">
                        @foreach($items as $item)
                            <a href="{{ \App\Support\Collections::itemUrl($type, $item->slug) }}"
                               class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-[#F6B828] hover:text-[#0A2240] text-white px-4 py-2 rounded-full text-xs font-bold transition-all duration-200">
                                <x-pp-icon name="users" :size="12" />
                                {{ $item->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
