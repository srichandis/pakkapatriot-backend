@extends('layouts.site')

@section('title', 'Create — The Pakka Maker\'s Space — Pakka Patriot')
@section('description', 'Draw, build, write, experiment and share your ideas. Bhārat has always been a land of makers — now it\'s your turn!')

{{--
    CREATE — the maker's space. Ported from the React CreatePage: hero with the
    multi-coloured wordmark and maker's desk, the twelve activity cards, the
    challenge / gallery / printables panels and the closing CTA banner.
--}}
@section('content')
<div class="min-h-screen bg-brand-cream font-sans">

    {{-- HERO --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.10] pointer-events-none select-none" aria-hidden="true">
            <x-pp-icon name="rocket" :size="90" class="absolute top-10 right-[12%] text-[#F97316] -rotate-12" />
            <x-pp-icon name="sparkles" :size="36" class="absolute top-24 left-[8%] text-[#14B8A6]" />
            <x-pp-icon name="star" :size="28" class="absolute top-40 right-[30%] text-[#EF4444]" fill="#EF4444" />
            <x-pp-icon name="star" :size="20" class="absolute bottom-24 left-[16%] text-[#F59E0B]" fill="#F59E0B" />
            <x-pp-icon name="send" :size="44" class="absolute bottom-16 right-[10%] text-[#6366F1] rotate-12" />
            <div class="absolute top-1/3 left-[45%] w-24 h-24 border-2 border-dashed border-[#F59E0B] rounded-full"></div>
            <div class="absolute bottom-10 left-[38%] w-16 h-16 border-2 border-dashed border-[#14B8A6] rounded-full"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 sm:pt-20 pb-14 sm:pb-20 grid lg:grid-cols-[1.05fr_0.95fr] gap-12 items-center">
            {{-- Left — copy --}}
            <div class="text-center lg:text-left">
                <span class="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-black tracking-[0.3em] uppercase text-[#0A2240] bg-white/70 border border-[#F0EBE0] px-4 py-1.5 rounded-full shadow-sm">
                    <x-pp-icon name="sparkles" :size="13" class="text-[#F6B828]" /> The Pakka Maker's Space
                </span>

                <h1 class="font-display font-black text-6xl sm:text-7xl lg:text-8xl leading-none tracking-tight mt-5 select-none" aria-label="CREATE">
                    @foreach($heroLetters as $letter)
                        <span class="inline-block" style="color: {{ $letter['colour'] }}">{{ $letter['ch'] }}</span>
                    @endforeach
                </h1>

                <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-[#0A2240] mt-3">
                    Make Bhārat with your hands!
                </h2>
                <p class="mt-4 max-w-xl mx-auto lg:mx-0 text-sm sm:text-base text-[#4E637A] font-medium leading-relaxed">
                    Draw, build, write, experiment and share your ideas. Bhārat has always been a land
                    of makers. Now it's your turn!
                </p>

                {{-- Process steps --}}
                <div class="mt-8 flex items-center justify-center lg:justify-start gap-2 sm:gap-3">
                    @foreach($processSteps as $step)
                        <div class="flex flex-col items-center gap-1.5 group">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl {{ $step['colour'] }} flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:-rotate-6 transition-transform">
                                <x-pp-icon :name="$step['icon']" :size="22" />
                            </div>
                            <span class="text-[10px] sm:text-xs font-black text-[#0A2240]">{{ $step['label'] }}</span>
                        </div>
                        @unless($loop->last)
                            <div class="flex-1 max-w-10 sm:max-w-14 border-t-2 border-dashed border-[#C8C5B9] mb-5 hidden sm:block"></div>
                        @endunless
                    @endforeach
                </div>
            </div>

            {{-- Right — maker's desk --}}
            <div class="relative hidden md:block">
                <div class="relative bg-gradient-to-br from-[#FFF3DC] via-[#FEF0D8] to-[#FCE9D0] border border-[#F0E0B8] rounded-[2.5rem] shadow-xl p-8 sm:p-10">
                    <div class="absolute inset-6 rounded-[2rem] border-2 border-dashed border-[#F6B828]/40 pointer-events-none" aria-hidden="true"></div>

                    <div class="relative mx-auto w-52 h-52 sm:w-60 sm:h-60 rounded-3xl bg-white shadow-2xl rotate-[-3deg] flex flex-col items-center justify-center border border-[#F0EBE0]">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#F97316] to-[#F6B828] flex items-center justify-center shadow-lg">
                            <x-pp-icon name="landmark" :size="30" class="text-white" />
                        </div>
                        <span class="text-3xl mt-3">🏛️</span>
                        <span class="text-[10px] font-black tracking-widest text-[#8A9EB4] uppercase mt-2">
                            Painting a wonder
                        </span>
                    </div>

                    @foreach([
                        ['icon' => 'palette', 'cls' => 'bg-[#F97316] top-6 left-6'],
                        ['icon' => 'pencil', 'cls' => 'bg-[#E11D48] top-4 right-10'],
                        ['icon' => 'scissors', 'cls' => 'bg-[#DB2777] top-24 right-2'],
                        ['icon' => 'flask-conical', 'cls' => 'bg-[#16A34A] bottom-6 right-8'],
                        ['icon' => 'hammer', 'cls' => 'bg-[#2563EB] bottom-16 left-4'],
                        ['icon' => 'dices', 'cls' => 'bg-[#7C3AED] top-1/2 -left-3'],
                        ['icon' => 'camera', 'cls' => 'bg-[#0D9488] bottom-2 left-24'],
                        ['icon' => 'clapperboard', 'cls' => 'bg-[#6366F1] -top-3 left-1/3'],
                    ] as $tile)
                        <div class="absolute w-11 h-11 rounded-2xl {{ $tile['cls'] }} flex items-center justify-center shadow-lg text-white">
                            <x-pp-icon :name="$tile['icon']" :size="17" />
                        </div>
                    @endforeach

                    <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-[#0A2240] text-[#F6B828] text-[11px] font-black tracking-widest uppercase px-5 py-2 rounded-full shadow-xl flex items-center gap-1.5 whitespace-nowrap">
                        <x-pp-icon name="heart" :size="12" class="text-[#EF4444]" fill="#EF4444" /> Made by makers, for makers
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ACTIVITY CARDS --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="flex items-end justify-between mb-7">
            <div>
                <span class="text-[10px] font-black tracking-[0.3em] uppercase text-[#F97316]">Pick an activity</span>
                <h2 class="font-display font-black text-2xl sm:text-3xl text-[#0A2240] mt-1">
                    What will you create today?
                </h2>
            </div>
            <span class="hidden sm:inline-flex items-center text-xs font-bold text-[#587760] bg-[#EAF1EB] px-3.5 py-1.5 rounded-full">
                {{ count($cards) }} maker activities
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($cards as $activity)
                <a href="{{ url('/create/activity/'.$activity['slug']) }}"
                   class="group relative bg-white rounded-3xl overflow-hidden border border-[#F0EBE0] shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 text-left flex flex-col">
                    {{-- Art tile --}}
                    <div class="relative h-32 bg-gradient-to-br {{ $activity['tile'] }} flex items-center justify-center overflow-hidden">
                        <div class="absolute -top-3 -right-3 w-16 h-16 rounded-full bg-white/40" aria-hidden="true"></div>
                        <div class="absolute -bottom-5 -left-4 w-14 h-14 rounded-full bg-white/30" aria-hidden="true"></div>
                        <span class="text-[3.25rem] leading-none drop-shadow-sm select-none" aria-hidden="true">{{ $activity['emoji'] }}</span>
                        <span class="absolute top-3 left-3 text-[10px] font-black {{ $activity['ring'] }} px-2 py-0.5 rounded-full">
                            {{ $activity['id'] }}
                        </span>
                    </div>

                    {{-- Body --}}
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="font-display font-black text-lg text-[#0A2240] leading-snug min-h-[3.4rem] flex items-start">
                            {{ $activity['title'] }}
                        </h3>
                        <p class="text-xs text-[#4E637A] font-medium leading-relaxed mt-1.5 flex-grow">
                            {{ $activity['description'] }}
                        </p>
                        <div class="mt-4 inline-flex items-center justify-center gap-1.5 text-white text-xs font-black tracking-wide px-4 py-2.5 rounded-full shadow-md transition-all duration-200 group-hover:shadow-lg group-hover:gap-2.5 {{ $activity['button'] }}">
                            {{ $activity['cta'] }} <x-pp-icon name="arrow-right" :size="14" />
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- BOTTOM PANELS --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-14 grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- This week's challenge --}}
        <div class="bg-[#FEF5E0] border border-[#F0E6C8] rounded-3xl p-6 flex flex-col shadow-sm">
            <span class="text-[10px] font-black tracking-[0.25em] uppercase text-[#B45309]">
                ⚡ This Week's Challenge
            </span>
            <div class="mt-4 h-32 rounded-2xl bg-gradient-to-br from-[#FFEDD5] to-[#FED7AA] flex items-center justify-center overflow-hidden relative">
                <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full bg-white/40" aria-hidden="true"></div>
                <span class="text-6xl drop-shadow select-none" aria-hidden="true">🌉</span>
            </div>
            <h3 class="font-display font-black text-xl text-[#0A2240] mt-4">Build a Paper Bridge</h3>
            <p class="text-xs text-[#4E637A] font-medium mt-1 flex-grow">
                Can you build a strong bridge using paper and tape?
            </p>
            <button type="button" data-toast="Challenge is live — grab paper & tape and start testing!"
                    class="mt-4 inline-flex items-center justify-center gap-1.5 bg-[#F97316] hover:bg-[#EA580C] text-white text-xs font-black tracking-wide px-5 py-3 rounded-full shadow-md hover:shadow-lg transition-all cursor-pointer">
                Start Challenge <x-pp-icon name="arrow-right" :size="14" />
            </button>
        </div>

        {{-- Made by Pakka Patriots --}}
        <div class="bg-white border border-[#F0EBE0] rounded-3xl p-6 flex flex-col shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black tracking-[0.25em] uppercase text-[#0A2240]">
                    🌟 Made by Pakka Patriots
                </span>
                <button type="button" data-toast="The gallery wall is going up — coming soon!"
                        class="text-[11px] font-black text-[#2563EB] hover:underline flex items-center gap-0.5 cursor-pointer">
                    View All <x-pp-icon name="arrow-right" :size="12" />
                </button>
            </div>

            <div class="mt-4 space-y-3 flex-grow">
                @foreach($creations as $creation)
                    <div class="flex items-center gap-3 bg-[#FCFAF5] border border-[#F0EBE0] rounded-2xl p-2.5 hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <div class="relative w-14 h-14 rounded-xl bg-gradient-to-br {{ $creation['tile'] }} flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl" aria-hidden="true">{{ $creation['emoji'] }}</span>
                            @if($creation['video'] ?? false)
                                <span class="absolute inset-0 bg-black/30 rounded-xl flex items-center justify-center">
                                    <span class="w-6 h-6 rounded-full bg-white flex items-center justify-center">
                                        <x-pp-icon name="play" :size="11" fill="#0A2240" class="text-[#0A2240] ml-0.5" />
                                    </span>
                                </span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="font-display font-bold text-sm text-[#0A2240] truncate">{{ $creation['title'] }}</p>
                            <p class="text-[11px] text-[#8A9EB4] font-semibold flex items-center gap-1">
                                by {{ $creation['by'] }} <span class="text-[#C8C5B9]">•</span>
                                <x-pp-icon name="map-pin" :size="10" /> {{ $creation['city'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Top printables --}}
        <div class="bg-white border border-[#F0EBE0] rounded-3xl p-6 flex flex-col shadow-sm">
            <span class="text-[10px] font-black tracking-[0.25em] uppercase text-[#0A2240]">
                🖨️ Top Printables
            </span>

            <div class="mt-4 space-y-2.5 flex-grow">
                @foreach($printables as $printable)
                    <button type="button" data-toast="Your printable is being printed — coming soon!"
                            class="w-full flex items-center gap-3 bg-[#FCFAF5] border border-[#F0EBE0] rounded-2xl p-2.5 hover:shadow-md hover:-translate-y-0.5 transition-all text-left cursor-pointer group">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-[#FAF6EC] to-[#F0E8D4] border border-[#E4DCB9] flex items-center justify-center flex-shrink-0">
                            <span class="text-xl" aria-hidden="true">{{ $printable['emoji'] }}</span>
                        </div>
                        <div class="min-w-0 flex-grow">
                            <p class="font-display font-bold text-xs text-[#0A2240] truncate">{{ $printable['title'] }}</p>
                            <p class="text-[10px] text-[#8A9EB4] font-semibold">Download PDF</p>
                        </div>
                        <span class="w-8 h-8 rounded-full bg-[#7C3AED] text-white flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                            <x-pp-icon name="download" :size="14" />
                        </span>
                    </button>
                @endforeach
            </div>

            <button type="button" data-toast="All printables coming soon!"
                    class="mt-4 w-full inline-flex items-center justify-center gap-1.5 bg-[#7C3AED] hover:bg-[#6D28D9] text-white text-xs font-black tracking-wide px-5 py-3 rounded-full shadow-md hover:shadow-lg transition-all cursor-pointer">
                View All Printables <x-pp-icon name="arrow-right" :size="14" />
            </button>
        </div>
    </section>

    {{-- CTA BANNER --}}
    <section class="bg-[#FEF5E0] border-y border-[#F0E6C8] overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-14 flex flex-col sm:flex-row items-center gap-8 text-center sm:text-left">
            <div class="flex-shrink-0">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-gradient-to-br from-[#F97316] to-[#F6B828] flex items-center justify-center text-5xl shadow-xl rotate-[-4deg]">
                    <span aria-hidden="true">🎉</span>
                </div>
            </div>
            <div class="flex-grow">
                <h2 class="font-display font-black text-3xl sm:text-4xl tracking-tight">
                    <span class="text-[#F97316]">Create.</span>
                    <span class="text-[#0D9488]">Share.</span>
                    <span class="text-[#E11D48]">Inspire.</span>
                </h2>
                <p class="mt-2 text-sm text-[#4E637A] font-semibold">
                    Your creation can inspire millions of young Pakka Patriots across Bhārat!
                </p>
            </div>
            <button type="button" data-toast="Share your creation with us — coming soon!"
                    class="flex-shrink-0 inline-flex items-center gap-2 bg-[#F97316] hover:bg-[#EA580C] text-white px-7 py-3.5 rounded-full font-black text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all cursor-pointer">
                Share Your Creation <x-pp-icon name="arrow-right" :size="16" />
            </button>
        </div>
    </section>
</div>
@endsection
