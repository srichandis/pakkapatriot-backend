@extends('layouts.site')

@section('title', 'All Stories — Pakka Patriot')
@section('description', 'Every story from the Pakka Patriot library — history, culture, heroes and the land we love.')

@php
    /**
     * Blog listing — ported from the React BlogsPage component.
     *
     * Filtering is server-side (search + category query string) so each filtered
     * view is linkable and crawlable; the search box still filters as you type
     * via the debounced submit in resources/js/app.js.
     */
    $chipUrl = fn (?string $cat) => route('shop.blog.index', array_filter([
        'category' => $cat,
        'q' => $search !== '' ? $search : null,
    ]));

    $clearSearchUrl = route('shop.blog.index', array_filter(['category' => $category !== '' ? $category : null]));
    $isFiltered = $search !== '' || $category !== '';
@endphp

@section('content')
    <div class="min-h-screen bg-brand-cream relative">
        {{-- Background decor --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-[#F6B828]/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-40 left-0 w-80 h-80 bg-[#F6B828]/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 relative z-10">

            {{-- Hero --}}
            <div class="text-center mb-10">
                <div class="inline-flex items-center gap-2 bg-brand-blue/5 rounded-full px-4 py-1.5 mb-4">
                    <x-pp-icon name="newspaper" :size="16" class="text-[#F6B828]" />
                    <span class="text-xs font-black tracking-widest text-brand-blue uppercase">Stories from Bhārat</span>
                </div>
                <h1 class="font-brush text-5xl sm:text-6xl lg:text-7xl text-brand-blue tracking-wide leading-tight mb-4">
                    All <span class="text-[#F6B828]">Stories</span>
                </h1>
                <p class="text-lg sm:text-xl text-[#4E637A] font-medium max-w-2xl mx-auto">
                    Every blog post from the Pakka Patriot library — {{ $total }} stories of history,
                    culture, heroes, and the land we love.
                </p>
            </div>

            {{-- Search + category filters --}}
            <div class="mb-10">
                <form action="{{ route('shop.blog.index') }}" method="GET" class="relative max-w-md mx-auto mb-6" data-blog-search>
                    @if($category !== '')
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif

                    <x-pp-icon name="search" :size="20" class="absolute left-4 top-1/2 -translate-y-1/2 text-[#8A9EB4]" />
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Search stories by title, category, or author..."
                           aria-label="Search stories"
                           class="w-full bg-white border border-[#E4DCB9] rounded-full pl-12 pr-10 py-3 text-sm font-semibold text-brand-blue focus:outline-none focus:border-[#F6B828] focus:ring-2 focus:ring-[#F6B828]/10 transition-all">
                    @if($search !== '')
                        <a href="{{ $clearSearchUrl }}" aria-label="Clear search"
                           class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8A9EB4] hover:text-[#F6B828] transition-colors">
                            <x-pp-icon name="x" :size="18" />
                        </a>
                    @endif
                </form>

                <div class="flex flex-wrap justify-center gap-2.5 select-none">
                    <a href="{{ $chipUrl(null) }}"
                       class="px-4 py-2 rounded-full text-xs font-bold tracking-wide transition-all duration-200 {{ $category === '' ? 'bg-brand-blue text-white shadow-md' : 'bg-white border border-[#E4DCB9] text-[#4E637A] hover:border-brand-blue hover:text-brand-blue' }}">
                        ALL ({{ $total }})
                    </a>

                    @foreach($categories as $cat)
                        <a href="{{ $chipUrl($cat['value']) }}"
                           class="px-4 py-2 rounded-full text-xs font-bold tracking-wide transition-all duration-200 {{ $category === $cat['value'] ? 'bg-brand-blue text-white shadow-md' : 'bg-white border border-[#E4DCB9] text-[#4E637A] hover:border-brand-blue hover:text-brand-blue' }}">
                            {{ $cat['label'] }} ({{ $cat['count'] }})
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Results count --}}
            <div class="flex justify-between items-center mb-6 px-1">
                <p class="text-sm font-semibold text-[#8A9EB4]">
                    {{ $posts->total() }} {{ $posts->total() === 1 ? 'story' : 'stories' }} found
                    @if($category !== '')
                        <span class="text-brand-blue"> in {{ mb_strtoupper($category) }}</span>
                    @endif
                </p>
            </div>

            {{-- Grid --}}
            @if($posts->isEmpty())
                <div class="text-center py-16 bg-white rounded-3xl border border-[#F0EBE0] max-w-md mx-auto">
                    <x-pp-icon name="book-open" :size="64" class="mx-auto text-[#E4DCB9] mb-4" />
                    <h3 class="font-display font-bold text-xl text-brand-blue mb-2">No Stories Found</h3>
                    <p class="text-sm text-[#4E637A] font-medium mb-6">
                        Try a different search or category filter.
                    </p>
                    <a href="{{ route('shop.blog.index') }}"
                       class="inline-block px-6 py-3 rounded-full bg-brand-blue text-white text-sm font-bold hover:bg-[#0A2240]/90 transition-colors">
                        Show all stories
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($posts as $post)
                        <x-pp-story-card :post="$post" />
                    @endforeach
                </div>

                @if($posts->hasPages())
                    <div class="mt-12">
                        {{ $posts->onEachSide(1)->links('pagination::tailwind') }}
                    </div>
                @endif
            @endif

            {{-- Bottom CTA --}}
            <div class="mt-16 text-center">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 bg-[#0A2240] hover:bg-[#1A3A5E] text-white px-8 py-4 rounded-xl text-md font-bold shadow-lg hover:shadow-xl transition-all duration-200 group">
                    <x-pp-icon name="book-open" :size="18" />
                    Back to Home
                    <x-pp-icon name="arrow-right" :size="18" class="transition-transform group-hover:translate-x-1" />
                </a>
            </div>
        </div>
    </div>
@endsection
