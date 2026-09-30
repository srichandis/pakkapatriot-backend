@php
    /**
     * One category section of a collection browse page: header + card grid.
     *
     * `$limit` caps the grid in the grouped ("ALL") view — People alone holds
     * 385 items, and rendering every card on one page produced a 2 MB document.
     * The section's full list lives one click away on its category URL.
     */
    $limit = $limit ?? null;
    $count = $section['items']->count();
    $visible = $limit ? $section['items']->take($limit) : $section['items'];
    $truncated = $limit !== null && $count > $visible->count();
@endphp

<div id="cat-{{ $type }}-{{ $section['id'] }}" class="scroll-mt-36">
    <div class="flex items-center gap-3 mb-6">
        <h2 class="font-display font-black text-xl sm:text-2xl text-[#0A2240] tracking-tight whitespace-nowrap">
            {{ $section['label'] }}
        </h2>
        <span class="text-[10px] font-black bg-[#F6B828]/15 text-[#B8860B] px-2.5 py-1 rounded-full whitespace-nowrap">
            {{ $count }} {{ $count === 1 ? $meta['itemNounSingular'] : $meta['itemNoun'] }}
        </span>
        <span class="flex-1 h-px bg-[#E4DCB9]"></span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($visible as $item)
            <x-pp-collection-card :item="$item" :type="$type" />
        @endforeach
    </div>

    @if($truncated)
        <div class="mt-6 text-center">
            <a href="{{ \App\Support\Collections::browseUrl($type).'?category='.urlencode($section['id']) }}#cat-{{ $type }}-{{ $section['id'] }}"
               class="inline-flex items-center gap-2 text-xs font-black tracking-widest uppercase text-[#0A2240] bg-white border border-[#E4DCB9] hover:border-[#F6B828] hover:bg-[#FEF5E0] px-5 py-2.5 rounded-full transition-all">
                See all {{ $count }} {{ $meta['itemNoun'] }}
                <x-pp-icon name="arrow-right" :size="14" />
            </a>
        </div>
    @endif
</div>
