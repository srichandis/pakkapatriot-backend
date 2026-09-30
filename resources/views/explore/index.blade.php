@extends('layouts.site')

@section('title', 'Explore Bhārat — Pakka Patriot')
@section('description', "Dive into stories about Bhārat's rich heritage, incredible people, vibrant traditions, and breathtaking places.")

@php
    $chipUrl = fn (?string $cat) => route('explore', array_filter([
        'category' => $cat,
        'q' => $search !== '' ? $search : null,
    ]));

    $clearSearchUrl = route('explore', array_filter(['category' => $category !== '' ? $category : null]));
@endphp

@section('content')
    <section class="bg-brand-cream min-h-screen relative overflow-hidden">
        {{-- Decorative background --}}
        <div class="absolute top-20 right-0 w-80 h-80 bg-[#F6B828]/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-40 left-0 w-64 h-64 bg-[#F6B828]/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 relative z-10">

            {{-- Hero --}}
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-brand-blue/5 rounded-full px-4 py-1.5 mb-4">
                    <x-pp-icon name="compass" :size="16" class="text-[#F6B828]" />
                    <span class="text-xs font-black tracking-widest text-brand-blue uppercase">Discover</span>
                </div>

                <h1 class="font-brush text-5xl sm:text-6xl lg:text-7xl text-brand-blue tracking-wide leading-tight mb-4">
                    Explore <span class="text-[#F6B828]">Bhārat</span>
                </h1>

                <p class="text-lg sm:text-xl text-[#4E637A] font-medium max-w-2xl mx-auto">
                    Dive into stories about Bhārat's rich heritage, incredible people, vibrant traditions,
                    and breathtaking places — all in one place.
                </p>

                <div class="flex flex-wrap justify-center gap-4 mt-6">
                    <div class="bg-white rounded-full px-4 py-2 border border-[#F0EBE0] shadow-sm text-sm font-semibold text-brand-blue">
                        📖 {{ $total }} Stories
                    </div>
                    <div class="bg-white rounded-full px-4 py-2 border border-[#F0EBE0] shadow-sm text-sm font-semibold text-brand-blue">
                        🏷️ {{ count($categories) }} Categories
                    </div>
                </div>
            </div>

            {{-- Search and filters --}}
            <div class="mb-10">
                <form action="{{ route('explore') }}" method="GET" class="relative max-w-md mx-auto mb-6" data-blog-search>
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

                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ $chipUrl(null) }}"
                       class="px-5 py-2 rounded-full text-xs font-bold tracking-wide transition-all duration-200 {{ $category === '' ? 'bg-brand-blue text-white shadow-md' : 'bg-white border border-[#E4DCB9] text-[#4E637A] hover:border-brand-blue hover:text-brand-blue' }}">
                        ALL STORIES
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ $chipUrl($cat['value']) }}"
                           class="px-5 py-2 rounded-full text-xs font-bold tracking-wide transition-all duration-200 {{ $category === $cat['value'] ? 'bg-brand-blue text-white shadow-md' : 'bg-white border border-[#E4DCB9] text-[#4E637A] hover:border-brand-blue hover:text-brand-blue' }}">
                            {{ $cat['label'] }}
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

            {{-- Posts grid --}}
            @if($posts->isEmpty())
                <div class="text-center py-16 bg-white rounded-3xl border border-[#F0EBE0]">
                    <x-pp-icon name="book-open" :size="64" class="mx-auto text-[#E4DCB9] mb-4" />
                    <h3 class="font-display font-bold text-xl text-brand-blue mb-2">No Stories Found</h3>
                    <p class="text-sm text-[#4E637A] font-medium">
                        {{ $search !== '' ? 'Try a different search term.' : 'Try a different category filter.' }}
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($posts as $post)
                        <a href="{{ route('shop.blog.show', $post['slug']) }}"
                           class="bg-white rounded-2xl overflow-hidden border border-[#F0EBE0] hover:border-[#F6B828]/40 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer group">
                            {{-- Featured image --}}
                            <div class="relative h-48 sm:h-52 overflow-hidden bg-[#FAF6EC]">
                                <img src="{{ $post['featured_image'] }}" alt="{{ $post['title'] }}"
                                     class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                                     loading="lazy" referrerpolicy="no-referrer">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
                                <div class="absolute top-4 left-4">
                                    <x-pp-badge :category="$post['category']" class="shadow-sm" />
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-5 sm:p-6">
                                <h3 class="font-display font-extrabold text-lg text-brand-blue tracking-tight leading-snug mb-2 group-hover:text-[#F6B828] transition-colors">
                                    {{ $post['title'] }}
                                </h3>
                                <p class="text-sm text-[#4E637A] font-medium leading-relaxed mb-4 line-clamp-2">
                                    {{ $post['excerpt'] }}
                                </p>

                                <div class="flex items-center justify-between pt-3 border-t border-[#F0EBE0]/80 text-[11px] font-bold text-[#8A9EB4] uppercase tracking-wide">
                                    <span class="flex items-center gap-1">
                                        <x-pp-icon name="user" :size="11" />
                                        {{ $post['author_name'] ?: 'PATRIOT' }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <x-pp-icon name="clock" :size="11" />
                                        {{ $post['read_time'] }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($posts->hasPages())
                    <div class="mt-12">
                        {{ $posts->onEachSide(1)->links('pagination::tailwind') }}
                    </div>
                @endif
            @endif

            {{-- Bottom CTA --}}
            @if($posts->total() > 0)
                <div class="mt-16 text-center">
                    <div class="bg-gradient-to-br from-brand-blue to-[#1A3A5C] rounded-3xl p-8 sm:p-10 shadow-xl relative overflow-hidden">
                        <div class="absolute -top-20 -right-20 w-64 h-64 bg-[#F6B828]/10 rounded-full blur-3xl pointer-events-none"></div>

                        <div class="relative z-10 space-y-4">
                            <h2 class="font-brush text-3xl sm:text-4xl text-white tracking-wide">
                                Want to <span class="text-[#F6B828]">contribute</span>?
                            </h2>
                            <p class="text-[#B5CADF] font-semibold text-sm max-w-md mx-auto">
                                Have a story about Bhārat's heritage, people, or traditions? Share it with us and
                                become a featured Pakka Patriot writer!
                            </p>
                            <a href="mailto:hello@pakkapatriot.com"
                               class="inline-flex items-center gap-2 bg-[#F6B828] hover:bg-[#DAA520] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-200 group">
                                <x-pp-icon name="book-open" :size="16" />
                                SUBMIT YOUR STORY
                                <x-pp-icon name="arrow-right" :size="16" class="transition-transform group-hover:translate-x-1" />
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
