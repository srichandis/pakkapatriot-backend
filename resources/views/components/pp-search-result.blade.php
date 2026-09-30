@props([
    'match',
    'icon',
    'product' => null,
])

@php
    /**
     * The body of one search result card. The surrounding element differs
     * (a link for pages, a modal trigger for store products), so only the
     * contents live here.
     */
@endphp

@if($product)
    <div class="w-16 h-16 rounded-xl overflow-hidden bg-[#FAF6EC] flex-shrink-0">
        <img src="{{ $match['image'] }}" alt="{{ $match['title'] }}"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" referrerpolicy="no-referrer">
    </div>
@else
    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#0A2240] to-[#1F3D5E] flex items-center justify-center flex-shrink-0">
        <x-pp-icon :name="$icon" :size="24" class="text-white" />
    </div>
@endif

<div class="flex-grow min-w-0">
    <div class="flex items-center gap-2">
        @if($match['category'])
            <span class="text-[9px] font-black tracking-widest text-[#587760] uppercase bg-[#EAF1EB] px-2 py-0.5 rounded-full flex-shrink-0">
                {{ $match['category'] }}
            </span>
        @endif
        <x-pp-icon name="arrow-right" :size="12" class="text-[#F6B828] opacity-0 group-hover:opacity-100 transition-opacity" />
    </div>
    <h3 class="font-display font-bold text-sm text-[#0A2240] group-hover:text-[#F6B828] transition-colors leading-snug mt-1 line-clamp-2">
        {{ $match['title'] }}
    </h3>
    <p class="text-[11px] text-[#8A9EB4] font-semibold mt-0.5">{{ $match['subtitle'] }}</p>
    <p class="text-xs text-[#4E637A] font-medium leading-relaxed mt-1 line-clamp-2">{{ $match['snippet'] }}</p>
</div>
