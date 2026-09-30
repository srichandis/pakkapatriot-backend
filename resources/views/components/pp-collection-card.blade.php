@props([
    'item',
    'type',
])

@php
    /**
     * A collection card — Ideas / Places / People / Culture / Create.
     *
     * Ported from the React CollectionBrowsePage card: gradient banner with the
     * category badge and icon, then the summary and era/attribution meta.
     */
    $gradient = \App\Support\Gradient::css($item->accent);
@endphp

<a href="{{ \App\Support\Collections::itemUrl($type, $item->slug) }}"
   class="group bg-white rounded-3xl overflow-hidden border border-[#F0EBE0] hover:border-[#F6B828]/40 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col h-full">
    {{-- Gradient header, with the uploaded photo as a cover when there is one --}}
    <div class="relative px-6 pt-8 pb-14 overflow-hidden" style="background: {{ $gradient }}">
        @if ($item->image_url)
            <img
                src="{{ $item->image_url }}"
                alt="{{ $item->name }}"
                loading="lazy"
                referrerpolicy="no-referrer"
                class="absolute inset-0 h-full w-full object-cover object-center pointer-events-none"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/25 to-transparent pointer-events-none"></div>
        @else
            <div class="absolute -top-8 -right-8 w-32 h-32 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute top-4 right-16 w-4 h-4 bg-white/20 rounded-full pointer-events-none"></div>
            <div class="absolute bottom-2 left-1/3 w-2.5 h-2.5 bg-white/20 rounded-full pointer-events-none"></div>
        @endif

        <div class="relative flex items-start justify-between">
            <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase bg-white/20 text-white backdrop-blur-sm select-none">
                {{ $item->category }}
            </span>
            <div class="w-14 h-14 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center text-white shadow-inner">
                <x-pp-icon :name="$item->icon ?: 'sparkles'" :size="26" />
            </div>
        </div>

        <h3 class="relative mt-6 font-display font-black text-2xl text-white tracking-tight leading-tight group-hover:tracking-normal transition-all">
            {{ $item->name }}
        </h3>
        @if($item->native_name)
            <p class="relative text-white/80 font-semibold text-sm mt-0.5">{{ $item->native_name }}</p>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-5 flex-grow flex flex-col items-start text-left">
        <p class="text-sm text-[#4E637A] font-medium leading-relaxed mb-4 line-clamp-3 flex-grow">
            {{ $item->summary }}
        </p>

        <div class="w-full space-y-2 pt-3 border-t border-[#F0EBE0]/80">
            @if($item->era)
                <div class="flex items-center gap-2 text-[11px] font-bold text-[#8A9EB4] uppercase tracking-wide">
                    <x-pp-icon name="clock" :size="12" class="text-amber-500" />
                    {{ $item->era }}
                </div>
            @endif
            @if($item->attribution)
                <div class="flex items-center gap-2 text-[11px] font-bold text-[#8A9EB4] uppercase tracking-wide truncate">
                    <x-pp-icon name="landmark" :size="12" class="text-amber-500" />
                    <span class="truncate">{{ $item->attribution }}</span>
                </div>
            @endif
        </div>

        <div class="w-full mt-4 flex items-center justify-between">
            <span class="inline-flex items-center gap-1 text-[11px] font-black tracking-widest text-[#F6B828] uppercase">
                Explore
            </span>
            <span class="w-9 h-9 bg-[#FCFAF5] border border-[#E4DCB9] group-hover:bg-[#F6B828] group-hover:border-[#F6B828] text-[#0A2240] group-hover:text-white rounded-full flex items-center justify-center transition-colors">
                <x-pp-icon name="arrow-up-right" :size="16" />
            </span>
        </div>
    </div>
</a>
