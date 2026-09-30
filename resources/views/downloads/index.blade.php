@extends('layouts.site')

@section('title', 'Downloads — Printables, Guides & Apps — Pakka Patriot')
@section('description', "Free printables from Pakka Patriot: worksheets, activity sheets, colouring pages, travel guides, shloka sheets, panchangam calendars, wallpapers and app downloads.")

{{--
    DOWNLOADS — the library hub. One card per material type; the catalogue
    lives in config/downloads.php and each card opens /downloads/{slug}.
--}}
@section('content')
<div class="bg-brand-cream min-h-screen">

    {{-- HERO --}}
    <section class="relative bg-[#0A2240] text-white overflow-hidden">
        <div class="absolute inset-0 opacity-[0.06] pointer-events-none select-none" aria-hidden="true">
            <div class="absolute top-8 left-8 w-40 h-40 border-t-4 border-r-4 border-white rounded-tr-full"></div>
            <div class="absolute bottom-6 right-10 w-56 h-56 border-b-4 border-l-4 border-white rounded-bl-full"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20 text-left">
            <span class="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-black tracking-[0.25em] uppercase text-[#F6B828] mb-3">
                <x-pp-icon name="download" :size="14" /> Free to Print
            </span>
            <h1 class="font-brush text-4xl sm:text-6xl lg:text-7xl leading-tight tracking-wide">
                Downloads for <span class="text-[#F6B828]">Curious Minds</span>
            </h1>
            <p class="mt-4 max-w-2xl text-sm sm:text-base text-[#B5CADF] font-medium leading-relaxed">
                Worksheets, activity sheets, colouring pages, verse sheets, wallpapers and more —
                everything here is free to print and share with your class, your family or yourself.
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <span class="bg-white/10 border border-white/15 rounded-full px-4 py-2 text-xs font-bold text-white">
                    {{ $categories->count() }} collections
                </span>
                <span class="bg-white/10 border border-white/15 rounded-full px-4 py-2 text-xs font-bold text-white">
                    {{ $total }} downloads
                </span>
            </div>
        </div>
    </section>

    {{-- CATEGORY CARDS --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($categories as $category)
                <a href="{{ route('downloads.category', $category['slug']) }}"
                   class="group bg-white rounded-3xl border border-[#F0EBE0] shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-6 flex flex-col text-left">

                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl text-2xl border mb-4 transition-transform duration-300 group-hover:scale-110"
                          style="background-color: {{ $category['accent'] }}14; border-color: {{ $category['accent'] }}33">
                        {{ $category['emoji'] }}
                    </span>

                    <h2 class="font-display font-black text-lg text-[#0A2240] tracking-tight">
                        {{ $category['title'] }}
                    </h2>

                    <p class="mt-2 text-sm text-[#4E637A] font-medium leading-relaxed flex-grow line-clamp-3">
                        {{ $category['blurb'] }}
                    </p>

                    <span class="mt-5 pt-4 border-t border-[#F0EBE0] flex items-center justify-between">
                        <span class="text-[11px] font-black uppercase tracking-widest text-[#8A9EB4]">
                            {{ $category['count'] }} {{ \Illuminate\Support\Str::plural('item', $category['count']) }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-sm font-black text-[#0A2240] bg-[#F6B828] group-hover:bg-[#DAA520] px-4 py-2 rounded-full transition-colors">
                            Browse
                            <x-pp-icon name="chevron-right" :size="14" />
                        </span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

</div>
@endsection
