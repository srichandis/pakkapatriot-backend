@extends('layouts.site')

@section('title', 'Play & Learn — Games from Bhārat — Pakka Patriot')
@section('description', 'Step into the playrooms of history. These games are reimagined from the courtyards, palaces and village squares of ancient Bhārat.')

{{--
    PLAY — the games index, ported from the React PlayPage. Boards come from the
    `games` table; the tag chips carry the lucide icon names the seeder stored.
--}}
@section('content')
<div class="bg-brand-cream min-h-screen">

    {{-- HERO --}}
    <section class="relative bg-[#0A2240] text-white overflow-hidden">
        <div class="absolute inset-0 opacity-[0.06] pointer-events-none select-none" aria-hidden="true">
            <div class="absolute top-8 left-8 w-40 h-40 border-t-4 border-r-4 border-white rounded-tr-full"></div>
            <div class="absolute bottom-6 right-10 w-56 h-56 border-b-4 border-l-4 border-white rounded-bl-full"></div>
            <div class="absolute top-1/3 right-1/4 w-24 h-24 border border-white rounded-full"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20 text-left">
            <span class="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-black tracking-[0.25em] uppercase text-[#F6B828] mb-3">
                <x-pp-icon name="gamepad-2" :size="14" /> Play &amp; Learn
            </span>
            <h1 class="font-brush text-4xl sm:text-6xl lg:text-7xl leading-tight tracking-wide">
                Games from <span class="text-[#F6B828]">Bharat</span>
            </h1>
            <p class="mt-4 max-w-2xl text-sm sm:text-base text-[#B5CADF] font-medium leading-relaxed">
                Step into the playrooms of history. These games are reimagined from the courtyards, palaces,
                and village squares of ancient Bhārat — now playable with family and friends, online or in person.
            </p>
        </div>
    </section>

    {{-- GAME CARDS --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-display font-black text-xl sm:text-2xl text-[#0A2240]">All Games</h2>
            <span class="text-xs font-bold text-[#587760] bg-[#EAF1EB] px-3 py-1 rounded-full">
                {{ count($games) }} {{ count($games) === 1 ? 'game' : 'games' }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($games as $game)
                @php
                    $tags = \App\Http\Controllers\PlayController::tags($game);
                    $heroIcon = $tags[0]['icon'] ?? 'sparkles';
                @endphp
                <a href="{{ url($game->path) }}"
                   class="group relative bg-white rounded-3xl overflow-hidden border border-[#F0EBE0] shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 text-left flex flex-col">
                    {{-- Banner art — the uploaded cover image when there is one --}}
                    <div class="relative h-44 flex items-center justify-center overflow-hidden"
                         style="background: {{ \App\Support\Gradient::css($game->accent) }}">
                        @if ($game->image_url)
                            <img
                                src="{{ $game->image_url }}"
                                alt="{{ $game->title }}"
                                loading="lazy"
                                referrerpolicy="no-referrer"
                                class="absolute inset-0 h-full w-full object-cover object-center pointer-events-none"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/25 to-transparent pointer-events-none"></div>
                        @else
                            <div class="absolute inset-0 opacity-[0.08] pointer-events-none" aria-hidden="true">
                                <div class="absolute top-4 left-6 w-20 h-20 border-2 border-white rounded-tr-full"></div>
                                <div class="absolute bottom-4 right-6 w-24 h-24 border-2 border-white rounded-bl-full"></div>
                            </div>
                            <div class="relative w-24 h-24 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-sm flex items-center justify-center shadow-xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <x-pp-icon :name="$heroIcon" :size="44" class="text-[#F6B828]" />
                            </div>
                        @endif
                        <span class="absolute top-4 left-4 text-[10px] font-black tracking-widest uppercase bg-[#F6B828] text-[#0A2240] px-2.5 py-1 rounded-full shadow">
                            {{ $game->badge }}
                        </span>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 flex flex-col flex-grow">
                        <h3 class="font-display font-black text-2xl text-[#0A2240] flex items-center gap-2">
                            {{ $game->title }}
                            <x-pp-icon name="sparkles" :size="18" class="text-[#F6B828] opacity-0 group-hover:opacity-100 transition-opacity" />
                        </h3>
                        <p class="text-xs font-bold text-[#587760] uppercase tracking-wider mt-0.5">{{ $game->tagline }}</p>
                        <p class="text-sm text-[#4E637A] font-medium leading-relaxed mt-3 flex-grow">{{ $game->description }}</p>

                        <div class="flex flex-wrap gap-1.5 mt-4">
                            @foreach($tags as $tag)
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#0A2240] bg-[#FAF6EC] border border-[#E4DCB9] px-2.5 py-1 rounded-full">
                                    <x-pp-icon :name="$tag['icon']" :size="12" class="text-[#F6B828]" />
                                    {{ $tag['label'] }}
                                </span>
                            @endforeach
                        </div>

                        <div class="mt-6 pt-4 border-t border-[#F0EBE0] flex items-center justify-between">
                            <span class="text-xs font-bold text-[#587760]">FREE TO PLAY</span>
                            <span class="inline-flex items-center gap-1 text-sm font-black text-[#0A2240] bg-[#F6B828] group-hover:bg-[#DAA520] px-5 py-2.5 rounded-full transition-colors">
                                Play Now <x-pp-icon name="chevron-right" :size="16" />
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach

            {{-- Coming soon --}}
            <div class="rounded-3xl border-2 border-dashed border-[#E4DCB9] bg-white/50 flex flex-col items-center justify-center p-8 min-h-[280px] text-center">
                <div class="w-14 h-14 rounded-full bg-[#FAF6EC] border border-[#E4DCB9] flex items-center justify-center mb-3">
                    <x-pp-icon name="gamepad-2" :size="22" class="text-[#C8C5B9]" />
                </div>
                <p class="font-display font-bold text-[#0A2240]">More games coming soon</p>
                <p class="text-xs text-[#8A9EB4] font-semibold mt-1">Chaupar &amp; more from Bhārat's past</p>
            </div>
        </div>
    </section>

    {{-- HOW TO PLAY --}}
    <section class="bg-[#FEF5E0] border-y border-[#F0E6C8]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach([
                ['step' => '1', 'title' => 'Pick your game', 'text' => 'Choose Chaukabaara from the playroom above.'],
                ['step' => '2', 'title' => 'Play together', 'text' => 'Host an online room & share the 4-letter code, or pass the screen for hot-seat.'],
                ['step' => '3', 'title' => 'Cast & conquer', 'text' => 'Throw the cowrie shells, race your pieces home, and be the first to finish.'],
            ] as $step)
                <div class="flex gap-3 items-start text-left">
                    <div class="w-9 h-9 rounded-full bg-[#0A2240] text-[#F6B828] font-black flex items-center justify-center flex-shrink-0 text-sm">
                        {{ $step['step'] }}
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-sm text-[#0A2240]">{{ $step['title'] }}</h4>
                        <p class="text-xs text-[#4E637A] font-medium mt-0.5 leading-relaxed">{{ $step['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
        <a href="{{ url('/play/chaukabaara') }}"
           class="inline-flex items-center gap-2 bg-[#F6B828] hover:bg-[#DAA520] text-white px-8 py-4 rounded-full font-black text-sm shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
            <x-pp-icon name="shell" :size="18" /> Start Playing Chaukabaara
        </a>
    </section>
</div>
@endsection
