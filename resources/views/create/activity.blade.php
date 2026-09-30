@extends('layouts.site')

@php
    /**
     * One maker activity — ported from the React CreateActivityPage.
     *
     * $activity is null when the slug is unknown; React rendered a friendly
     * "still being crafted" page for that case (and this one answers 404).
     */
    if ($activity) {
        $heroGradient = \App\Support\Gradient::css($activity->hero_accent, 'linear-gradient(135deg, #0A2240, #1F3D5E)');
        $tileGradient = \App\Support\Gradient::css($activity->tile);
        $knownFor = is_array($activity->known_for) ? $activity->known_for : [];
        $related = is_array($activity->related) ? $activity->related : [];
        $tryThis = is_array($activity->try_this) ? $activity->try_this : [];
    }
@endphp

@if(! $activity)
    @section('title', 'Activity not found — Pakka Patriot')

    @section('content')
        <div class="min-h-[60vh] bg-brand-cream flex flex-col items-center justify-center px-6 text-center">
            <span class="text-6xl" aria-hidden="true">🧭</span>
            <h1 class="font-display font-black text-2xl text-[#0A2240] mt-4">
                This activity is still being crafted
            </h1>
            <p class="text-sm text-[#4E637A] font-medium mt-2 max-w-sm">
                We couldn't find that maker activity. Head back to the create space and
                pick another one!
            </p>
            <a href="{{ url('/create') }}"
               class="mt-6 inline-flex items-center gap-1.5 bg-[#0A2240] hover:bg-[#1F3D5E] text-white px-6 py-3 rounded-full font-black text-sm shadow-lg transition-all">
                <x-pp-icon name="arrow-left" :size="16" /> Back to Create
            </a>
        </div>
    @endsection
@else
    @section('title', $activity->title.' — Create — Pakka Patriot')
    @section('description', $activity->tagline)

    @section('content')
    <div class="min-h-screen bg-brand-cream font-sans">

        {{-- HERO --}}
        <section class="relative text-white overflow-hidden" style="background: {{ $heroGradient }}">
            <div class="absolute inset-0 opacity-[0.10] pointer-events-none select-none" aria-hidden="true">
                <div class="absolute top-8 left-8 w-40 h-40 border-t-4 border-r-4 border-white rounded-tr-full"></div>
                <div class="absolute bottom-6 right-10 w-56 h-56 border-b-4 border-l-4 border-white rounded-bl-full"></div>
                <div class="absolute top-1/3 right-1/4 w-24 h-24 border border-white rounded-full"></div>
                <x-pp-icon name="sparkles" :size="40" class="absolute top-16 right-[18%]" />
                <x-pp-icon name="sparkles" :size="24" class="absolute bottom-16 left-[12%]" />
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
                <a href="{{ url('/create') }}"
                   class="inline-flex items-center gap-1.5 text-[11px] font-black tracking-widest uppercase text-white/80 hover:text-white transition-colors mb-8">
                    <x-pp-icon name="arrow-left" :size="14" /> Back to Create
                </a>

                <div class="flex flex-col sm:flex-row items-start gap-6 sm:gap-8">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl flex items-center justify-center text-6xl shadow-2xl flex-shrink-0 border-4 border-white/40 rotate-[-4deg]"
                         style="background: {{ $tileGradient }}">
                        <span aria-hidden="true">{{ $activity->emoji ?: '🛠️' }}</span>
                    </div>

                    <div class="min-w-0">
                        <span class="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-black tracking-[0.3em] uppercase bg-white/20 backdrop-blur-sm border border-white/30 px-4 py-1.5 rounded-full">
                            <x-pp-icon name="sparkles" :size="13" /> Maker Activity · {{ $activity->badge }}
                        </span>
                        <h1 class="font-display font-black text-4xl sm:text-5xl lg:text-6xl tracking-tight mt-4">
                            {{ $activity->title }}
                        </h1>
                        @if($activity->tagline)
                            <p class="mt-3 max-w-2xl text-sm sm:text-base text-white/90 font-semibold leading-relaxed">
                                {{ $activity->tagline }}
                            </p>
                        @endif
                    </div>
                </div>

                @if($activity->what_is)
                    <p class="mt-8 max-w-3xl text-sm sm:text-base text-white/85 font-medium leading-relaxed bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-5">
                        {{ $activity->what_is }}
                    </p>
                @endif
            </div>
        </section>

        {{-- BEST KNOWN FOR --}}
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="max-w-2xl">
                <span class="text-[10px] font-black tracking-[0.3em] uppercase text-[#F97316]">
                    Best known for
                </span>
                <h2 class="font-display font-black text-2xl sm:text-3xl text-[#0A2240] mt-1">
                    Bhārat is famous for this — and so can you be
                </h2>
                <p class="text-sm text-[#4E637A] font-medium mt-2">
                    Every great craft has a story. These are the things {{ mb_strtolower($activity->title) }} is
                    best known for across Bhārat's heritage.
                </p>
            </div>

            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($knownFor as $item)
                    <div class="group bg-white rounded-3xl border border-[#F0EBE0] p-6 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl shadow-md group-hover:scale-110 group-hover:-rotate-6 transition-transform"
                             style="background: {{ $tileGradient }}">
                            <span aria-hidden="true">{{ $item['emoji'] ?? '✨' }}</span>
                        </div>
                        <h3 class="font-display font-black text-lg text-[#0A2240] mt-4">
                            {{ $item['title'] ?? '' }}
                        </h3>
                        <p class="text-sm text-[#4E637A] font-medium leading-relaxed mt-1.5">
                            {{ $item['text'] ?? '' }}
                        </p>
                    </div>
                @endforeach

                @if($tryThis)
                    <div class="bg-[#FEF5E0] border-2 border-dashed border-[#E4D9A8] rounded-3xl p-6 flex flex-col shadow-sm">
                        <div class="w-14 h-14 rounded-2xl bg-[#0A2240] text-[#F6B828] flex items-center justify-center shadow-md">
                            <x-pp-icon name="lightbulb" :size="26" />
                        </div>
                        <span class="text-[10px] font-black tracking-[0.25em] uppercase text-[#B45309] mt-4">
                            ⚡ Try this at home
                        </span>
                        <h3 class="font-display font-black text-lg text-[#0A2240] mt-1">
                            {{ $tryThis['title'] ?? '' }}
                        </h3>
                        <p class="text-sm text-[#4E637A] font-medium leading-relaxed mt-1.5 flex-grow">
                            {{ $tryThis['text'] ?? '' }}
                        </p>
                        <span class="mt-4 text-[11px] font-black text-[#587760]">
                            No fancy materials needed — just you and your imagination.
                        </span>
                    </div>
                @endif
            </div>
        </section>

        {{-- KEEP EXPLORING + CTA --}}
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-14">
            @if($related)
                <div class="bg-white border border-[#F0EBE0] rounded-3xl p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center gap-2">
                        <x-pp-icon name="compass" :size="18" class="text-[#F6B828]" />
                        <span class="text-[10px] font-black tracking-[0.3em] uppercase text-[#0A2240]">
                            Keep exploring
                        </span>
                    </div>
                    <p class="text-sm text-[#4E637A] font-medium mt-1.5">
                        Love {{ mb_strtolower($activity->title) }}? Go deeper with these Pakka Patriot pages.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach($related as $link)
                            <a href="{{ url($link['path'] ?? '#') }}"
                               class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0A2240] bg-[#FAF6EC] border border-[#E4DCB9] px-4 py-2.5 rounded-full hover:bg-[#0A2240] hover:text-white hover:border-[#0A2240] transition-all">
                                {{ $link['label'] ?? '' }} <x-pp-icon name="arrow-right" :size="13" />
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-8 text-center">
                <a href="{{ url('/create') }}"
                   class="inline-flex items-center gap-2 text-white px-8 py-4 rounded-full font-black text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all"
                   style="background: {{ $heroGradient }}">
                    <span aria-hidden="true">{{ $activity->emoji ?: '🛠️' }}</span> More things to create
                </a>
            </div>
        </section>
    </div>
    @endsection
@endif
