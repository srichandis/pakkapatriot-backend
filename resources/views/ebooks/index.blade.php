@extends('layouts.site')

@section('title', 'Story Books & eBooks — Pakka Patriot')
@section('description', "Discover, learn, and inspire through {$total}+ free eBooks celebrating Bhārat's greatest freedom fighters, poets, scientists, and saints.")

@php
    // Category chips carry an emoji + colour, mirroring the React StoriesPage.
    $categoryMeta = [
        'Freedom Fighters' => ['icon' => '🇮🇳', 'color' => 'text-brand-blue'],
        'Poets' => ['icon' => '✍️', 'color' => 'text-purple-600'],
        'Scientists' => ['icon' => '🔬', 'color' => 'text-emerald-600'],
        'Saints' => ['icon' => '🕉️', 'color' => 'text-orange-500'],
    ];

    $chipUrl = fn (?string $cat) => route('stories', array_filter([
        'category' => $cat,
        'q' => $search !== '' ? $search : null,
    ]));

    $clearSearchUrl = route('stories', array_filter(['category' => $category !== '' ? $category : null]));
@endphp

@section('content')
    <section class="bg-brand-cream min-h-screen relative overflow-hidden">
        {{-- Subtle background decorations --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-[#F6B828]/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-40 left-0 w-80 h-80 bg-[#F6B828]/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 relative z-10">

            {{-- Hero --}}
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-brand-blue/5 rounded-full px-4 py-1.5 mb-4">
                    <x-pp-icon name="book-open" :size="16" class="text-[#F6B828]" />
                    <span class="text-xs font-black tracking-widest text-brand-blue uppercase">Free Library</span>
                </div>

                <h1 class="font-brush text-5xl sm:text-6xl lg:text-7xl text-brand-blue tracking-wide leading-tight mb-4">
                    Story Books & <span class="text-[#F6B828]">eBooks</span>
                </h1>

                <p class="text-lg sm:text-xl text-[#4E637A] font-medium max-w-2xl mx-auto">
                    Discover, Learn, and Inspire through <strong class="text-brand-blue">{{ $total }}+ free eBooks</strong> celebrating
                    Bhārat's greatest freedom fighters, poets, scientists, and saints.
                </p>

                {{-- Category stats --}}
                <div class="flex flex-wrap justify-center gap-4 mt-8">
                    @foreach($categories as $cat)
                        <div class="bg-white rounded-full px-4 py-2 border border-[#F0EBE0] shadow-sm flex items-center gap-2 text-sm">
                            <span>{{ $categoryMeta[$cat['label']]['icon'] ?? '📖' }}</span>
                            <span class="font-bold text-brand-blue">{{ $cat['label'] }}</span>
                            <span class="text-[#8A9EB4] font-semibold">· {{ $cat['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Search and filters --}}
            <div class="mb-10">
                <form action="{{ route('stories') }}" method="GET" class="relative max-w-md mx-auto mb-6" data-blog-search>
                    @if($category !== '')
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif

                    <x-pp-icon name="search" :size="20" class="absolute left-4 top-1/2 -translate-y-1/2 text-[#8A9EB4]" />
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Search books by name, era, or description..."
                           aria-label="Search eBooks"
                           class="w-full bg-white border border-[#E4DCB9] rounded-full pl-12 pr-10 py-3 text-sm font-semibold text-brand-blue focus:outline-none focus:border-[#F6B828] focus:ring-2 focus:ring-[#F6B828]/10 transition-all">
                    @if($search !== '')
                        <a href="{{ $clearSearchUrl }}" aria-label="Clear search"
                           class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8A9EB4] hover:text-[#F6B828] transition-colors">
                            <x-pp-icon name="x" :size="18" />
                        </a>
                    @endif
                </form>

                <div class="flex flex-wrap justify-center gap-3 select-none">
                    <a href="{{ $chipUrl(null) }}"
                       class="px-5 py-2 rounded-full text-xs font-bold tracking-wide transition-all duration-200 {{ $category === '' ? 'bg-brand-blue text-white shadow-md' : 'bg-white border border-[#E4DCB9] text-[#4E637A] hover:border-brand-blue hover:text-brand-blue' }}">
                        <x-pp-icon name="filter" :size="12" class="inline mr-1.5" />
                        ALL BOOKS
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ $chipUrl($cat['label']) }}"
                           class="px-5 py-2 rounded-full text-xs font-bold tracking-wide transition-all duration-200 flex items-center gap-1.5 {{ $category === $cat['label'] ? 'bg-brand-blue text-white shadow-md' : 'bg-white border border-[#E4DCB9] text-[#4E637A] hover:border-brand-blue hover:text-brand-blue' }}">
                            <span>{{ $categoryMeta[$cat['label']]['icon'] ?? '📖' }}</span>
                            {{ $cat['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Results count --}}
            <div class="flex justify-between items-center mb-6 px-1">
                <p class="text-sm font-semibold text-[#8A9EB4]">
                    {{ $books->count() }} {{ $books->count() === 1 ? 'book' : 'books' }} found
                    @if($category !== '')
                        <span class="text-brand-blue"> in {{ $category }}</span>
                    @endif
                </p>
            </div>

            {{-- Books grid --}}
            @if($books->isEmpty())
                <div class="text-center py-16 bg-white rounded-3xl border border-[#F0EBE0]">
                    <x-pp-icon name="book-open" :size="64" class="mx-auto text-[#E4DCB9] mb-4" />
                    <h3 class="font-display font-bold text-xl text-brand-blue mb-2">No Books Found</h3>
                    <p class="text-sm text-[#4E637A] font-medium">
                        Try a different search or category filter.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($books as $book)
                        <div class="group h-full">
                            <div class="bg-white rounded-2xl overflow-hidden border border-[#F0EBE0] hover:border-[#F6B828]/30 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 h-full flex flex-col">

                                {{-- Cover --}}
                                <div class="p-5 sm:p-6 min-h-[160px] flex flex-col justify-between relative overflow-hidden"
                                     style="background: {{ \App\Support\Gradient::css($book->cover_color) }}">
                                    {{-- Decorative dot pattern --}}
                                    <div class="absolute top-2 right-2 opacity-10">
                                        <div class="grid grid-cols-4 gap-1.5">
                                            @for($i = 0; $i < 16; $i++)
                                                <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="relative z-10">
                                        <span class="text-3xl sm:text-4xl">{{ $book->cover_emoji ?: '📖' }}</span>
                                    </div>

                                    <div class="relative z-10 mt-auto">
                                        <span class="text-[10px] font-black tracking-widest text-white/70 uppercase">
                                            {{ $book->category }}
                                        </span>
                                        <h3 class="font-display font-bold text-base sm:text-lg text-white leading-tight mt-0.5">
                                            {{ $book->title }}
                                        </h3>
                                        @if($book->subtitle)
                                            <p class="text-xs text-white/80 font-medium mt-0.5 line-clamp-1">
                                                {{ $book->subtitle }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Info --}}
                                <div class="p-4 sm:p-5 flex-grow flex flex-col">
                                    @if($book->era)
                                        <p class="text-xs text-[#8A9EB4] font-bold mb-1.5 font-mono">{{ $book->era }}</p>
                                    @endif
                                    <p class="text-xs text-[#4E637A] font-medium leading-relaxed flex-grow line-clamp-3">
                                        {{ $book->description }}
                                    </p>
                                    <div class="mt-4 pt-3 border-t border-[#F0EBE0] flex items-center justify-between">
                                        <span class="text-[10px] font-black tracking-widest text-brand-sage uppercase flex items-center gap-1">
                                            <x-pp-icon name="sparkles" :size="10" />
                                            Free eBook
                                        </span>
                                        <button type="button" data-open-journey
                                                class="flex items-center gap-1 text-xs font-bold text-[#F6B828] hover:text-[#DAA520] transition-colors group/download">
                                            <x-pp-icon name="download" :size="12" class="transition-transform group-hover/download:translate-y-0.5" />
                                            Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Bottom CTA --}}
            <div class="mt-16 text-center">
                <div class="bg-gradient-to-br from-brand-blue to-[#1A3A5C] rounded-3xl p-8 sm:p-12 shadow-xl relative overflow-hidden">
                    <div class="absolute -top-20 -right-20 w-64 h-64 bg-[#F6B828]/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-[#F6B828]/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 max-w-2xl mx-auto space-y-5">
                        <x-pp-icon name="star" :size="40" fill="#F6B828" class="text-[#F6B828] mx-auto" />
                        <h2 class="font-brush text-4xl sm:text-5xl text-white tracking-wide">
                            Want More <span class="text-[#F6B828]">Stories</span>?
                        </h2>
                        <p class="text-[#B5CADF] font-semibold text-lg max-w-lg mx-auto">
                            Get new eBooks, stories, and resources delivered to your inbox every week.
                            Join thousands of curious minds on the journey!
                        </p>
                        <button type="button" data-open-journey
                                class="inline-flex items-center gap-2 bg-[#F6B828] hover:bg-[#DAA520] text-white px-8 py-4 rounded-xl text-md font-bold shadow-lg hover:shadow-xl transition-all duration-200 group">
                            <x-pp-icon name="book-open" :size="18" />
                            GET FREE ACCESS
                            <x-pp-icon name="arrow-right" :size="18" class="transition-transform group-hover:translate-x-1" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
