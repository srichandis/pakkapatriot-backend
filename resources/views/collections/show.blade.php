@extends('layouts.site')

@section('title', $item->name.' — Pakka Patriot')
@section('description', $item->summary)

@php
    /**
     * A collection detail page — one Idea / Place / Person / Culture item /
     * Creation. Ported from the React CollectionDetailPage.
     */
    $gradient = \App\Support\Gradient::css($item->accent);
    $browseUrl = \App\Support\Collections::browseUrl($type);

    $facts = array_values(array_filter([
        ['icon' => 'calendar', 'label' => $meta['eraLabel'], 'value' => $item->era],
        ['icon' => 'landmark', 'label' => $meta['attributionLabel'], 'value' => $item->attribution],
        ['icon' => 'map-pin', 'label' => $meta['regionLabel'], 'value' => $item->region],
        ['icon' => 'sparkles', 'label' => $meta['categoryLabel'], 'value' => $item->category],
    ], fn ($fact) => filled($fact['value'])));

    $overview = array_values(array_filter($item->overview ?? []));
    $coreIdeas = array_values(array_filter($item->core_ideas ?? []));
@endphp

@section('content')
<div class="min-h-screen bg-brand-cream relative overflow-hidden">

    {{-- Sticky back bar --}}
    <div class="sticky top-0 z-20 bg-white/90 backdrop-blur-md border-b border-[#F0EBE0]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="{{ $browseUrl }}"
               class="flex items-center gap-2 text-sm font-bold text-[#0A2240] hover:text-[#F6B828] transition-colors">
                <x-pp-icon name="arrow-left" :size="18" />
                Back
            </a>
            <span class="text-[10px] font-black tracking-widest text-[#8A9EB4] uppercase">
                Pakka Patriot · {{ $meta['navLabel'] }}
            </span>
        </div>
    </div>

    {{-- Hero --}}
    <div class="relative text-white overflow-hidden" style="background: {{ $gradient }}">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full pointer-events-none"></div>
        <div class="absolute top-10 right-40 w-6 h-6 bg-white/20 rounded-full pointer-events-none"></div>
        <div class="absolute bottom-16 left-1/4 w-3 h-3 bg-white/20 rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-black/10 rounded-full pointer-events-none"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-8 py-12 sm:py-16 relative z-10 text-left">
            <div class="flex items-center gap-2 mb-6">
                <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase bg-white/20 text-white backdrop-blur-sm select-none">
                    {{ $item->category }}
                </span>
                <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase bg-white/20 text-white backdrop-blur-sm select-none">
                    {{ $meta['badgeLabel'] }}
                </span>
            </div>

            <div class="flex items-center gap-4 sm:gap-6">
                @if ($item->image_url)
                    <img
                        src="{{ $item->image_url }}"
                        alt="{{ $item->name }}"
                        referrerpolicy="no-referrer"
                        class="w-16 h-16 sm:w-20 sm:h-20 shrink-0 rounded-3xl border-2 border-white/40 object-cover object-center shadow-lg"
                    />
                @else
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/15 backdrop-blur-sm rounded-3xl flex items-center justify-center shadow-inner shrink-0">
                        <x-pp-icon :name="$item->icon ?: $meta['heroIcon']" :size="36" class="sm:hidden" />
                        <x-pp-icon :name="$item->icon ?: $meta['heroIcon']" :size="44" class="hidden sm:block" />
                    </div>
                @endif
                <div>
                    <h1 class="font-display font-black text-3xl sm:text-5xl tracking-tight leading-tight">
                        {{ $item->name }}
                    </h1>
                    @if($item->native_name)
                        <p class="text-white/80 font-semibold text-base sm:text-lg mt-1">{{ $item->native_name }}</p>
                    @endif
                </div>
            </div>

            @if($item->tagline)
                <p class="text-white/90 text-lg sm:text-xl font-medium mt-6 max-w-2xl leading-relaxed">
                    {{ $item->tagline }}
                </p>
            @endif
        </div>
    </div>

    {{-- Key facts strip --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-8 -mt-7 relative z-10">
        <div class="bg-white rounded-3xl border border-[#F0EBE0] shadow-lg p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($facts as $fact)
                <div class="flex items-start gap-3 text-left">
                    <div class="p-2.5 rounded-xl bg-[#FAF6EC] text-[#0A2240] shrink-0">
                        <x-pp-icon :name="$fact['icon']" :size="18" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black tracking-widest text-[#8A9EB4] uppercase">{{ $fact['label'] }}</p>
                        <p class="text-sm font-bold text-[#0A2240] mt-0.5 leading-snug">{{ $fact['value'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-8 py-10 sm:py-14">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12 items-start">

            {{-- Main column --}}
            <div class="lg:col-span-2 space-y-10">
                @if($item->quote && $item->quote_source)
                    <div class="relative bg-[#FAF6EC] border-l-4 border-[#F6B828] rounded-r-2xl p-6 sm:p-8">
                        <x-pp-icon name="quote" :size="32" class="absolute top-5 right-5 text-[#F6B828]/30" />
                        <p class="font-display font-bold text-lg sm:text-xl text-[#0A2240] italic leading-relaxed">
                            “{{ $item->quote }}”
                        </p>
                        <p class="text-xs font-black tracking-widest text-[#8A9EB4] uppercase mt-3">
                            — {{ $item->quote_source }}
                        </p>
                    </div>
                @endif

                {{-- Map: places with coordinates get an embedded map --}}
                @if($item->latitude && $item->longitude)
                    <div class="overflow-hidden rounded-3xl border border-[#F0EBE0] bg-white shadow-sm">
                        <iframe title="Map of {{ $item->name }}"
                                src="https://maps.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}&z=12&output=embed"
                                class="w-full h-64 sm:h-80 border-0" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    </div>
                @endif

                @if(!empty($overview))
                    <div class="space-y-5">
                        <h2 class="font-display font-black text-2xl sm:text-3xl text-[#0A2240] tracking-tight">
                            The <span class="text-[#F6B828]">Story</span>
                        </h2>
                        @foreach($overview as $para)
                            <p class="text-[#2F445A] leading-relaxed text-[15px] sm:text-base">{{ $para }}</p>
                        @endforeach
                    </div>
                @endif

                @if(!empty($coreIdeas))
                    <div>
                        <h2 class="font-display font-black text-2xl sm:text-3xl text-[#0A2240] tracking-tight mb-5">
                            Core <span class="text-[#F6B828]">Ideas</span>
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($coreIdeas as $idea)
                                <div class="bg-white rounded-2xl border border-[#F0EBE0] p-5 hover:border-[#F6B828]/40 hover:shadow-md transition-all duration-300">
                                    <div class="flex items-start gap-3">
                                        <x-pp-icon name="check-circle-2" :size="18" class="text-amber-500 mt-0.5" />
                                        <div>
                                            <h3 class="font-display font-bold text-sm text-[#0A2240] mb-1">{{ $idea['title'] ?? '' }}</h3>
                                            <p class="text-xs text-[#4E637A] font-medium leading-relaxed">{{ $idea['text'] ?? '' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Side column --}}
            <div class="space-y-6 lg:sticky lg:top-24">
                @if($item->legacy)
                    <div class="bg-gradient-to-br from-[#0A2240] to-[#1A3A5C] rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-[#F6B828]/10 rounded-full pointer-events-none"></div>
                        <div class="relative">
                            <div class="w-11 h-11 bg-[#F6B828] rounded-2xl flex items-center justify-center mb-4">
                                <x-pp-icon name="trophy" :size="20" class="text-[#0A2240]" />
                            </div>
                            <h3 class="font-display font-black text-lg tracking-tight mb-2">
                                Why it matters <span class="text-[#F6B828]">today</span>
                            </h3>
                            <p class="text-sm text-[#B5CADF] font-medium leading-relaxed">{{ $item->legacy }}</p>
                        </div>
                    </div>
                @endif

                @if($related->isNotEmpty())
                    <div class="bg-white rounded-3xl border border-[#F0EBE0] p-6">
                        <h3 class="font-display font-black text-base text-[#0A2240] tracking-tight mb-4">
                            Explore more {{ $meta['itemNoun'] }}
                        </h3>
                        <div class="space-y-3">
                            @foreach($related as $r)
                                <a href="{{ \App\Support\Collections::itemUrl($type, $r->slug) }}"
                                   class="group flex items-center gap-3 p-3 rounded-2xl border border-transparent hover:border-[#F6B828]/40 hover:bg-[#FCFAF5] transition-all duration-200">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0"
                                         style="background: {{ \App\Support\Gradient::css($r->accent) }}">
                                        <x-pp-icon :name="$r->icon ?: $meta['heroIcon']" :size="18" />
                                    </div>
                                    <div class="min-w-0 flex-grow text-left">
                                        <p class="font-display font-bold text-sm text-[#0A2240] truncate group-hover:text-[#F6B828] transition-colors">
                                            {{ $r->name }}
                                        </p>
                                        <p class="text-[10px] font-black tracking-widest text-[#8A9EB4] uppercase">
                                            {{ $r->category }}
                                        </p>
                                    </div>
                                    <x-pp-icon name="arrow-up-right" :size="16" class="text-[#8A9EB4] group-hover:text-[#F6B828] transition-colors" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Bottom navigation --}}
        <div class="pt-10 mt-4 border-t border-[#F0EBE0] flex flex-col sm:flex-row justify-between items-center gap-4">
            <a href="{{ $browseUrl }}"
               class="flex items-center gap-2 text-sm font-bold text-[#0A2240] hover:text-[#F6B828] transition-colors">
                <x-pp-icon name="arrow-left" :size="16" />
                All {{ $meta['itemNoun'] }}
            </a>

            @if($related->isNotEmpty())
                <a href="{{ \App\Support\Collections::itemUrl($type, $related->first()->slug) }}"
                   class="group inline-flex items-center gap-2 bg-[#F6B828] hover:bg-[#DAA520] text-white px-6 py-3 rounded-full text-sm font-bold shadow-md hover:shadow-lg transition-all duration-200">
                    Next: {{ $related->first()->name }}
                    <x-pp-icon name="arrow-right" :size="16" class="transition-transform group-hover:translate-x-1" />
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
