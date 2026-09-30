@props(['category'])

@php
    /**
     * Category → badge colour, mirroring the React `getCategoryBadgeClasses`
     * helper. Anything that isn't one of the four mapped families falls back
     * to the brand yellow.
     */
    $norm = mb_strtoupper(trim((string) $category));

    $palette = match (true) {
        str_contains($norm, 'HERITAGE') => 'bg-[#587760] text-white',
        str_contains($norm, 'PLACE') => 'bg-[#0A2240] text-white',
        str_contains($norm, 'TRADITION') => 'bg-[#D45012] text-white',
        str_contains($norm, 'PEOPLE') => 'bg-[#6D28D9] text-white',
        default => 'bg-[#F6B828] text-white',
    };
@endphp

<span {{ $attributes->merge(['class' => 'px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase select-none '.$palette]) }}>
    {{ $category }}
</span>
