@php
    /*
     * The three-column strip from design/popular.jpg: Popular Reads beside
     * Recent in Places and Recent in Culture, each headed by an icon and a
     * "View all" link, with four rows of thumbnail + title underneath.
     *
     * All three columns read the same collections the /people, /places and
     * /culture pages are built from. Collection items carry no imagery of their
     * own, so each thumbnail reuses the treatment the browse pages give them —
     * the item's accent gradient with its lucide icon on top.
     */
    $collectionRows = fn (string $type, string $fallbackIcon, $items) => collect($items ?? [])
        ->map(fn ($item) => [
            'href' => \App\Support\Collections::itemUrl($type, $item->slug),
            'title' => $item->name,
            'meta' => $item->category ?? '',
            'gradient' => \App\Support\Gradient::css($item->accent),
            'icon' => $item->icon ?: $fallbackIcon,
        ])
        ->all();

    $columns = [
        [
            'title' => 'Popular Reads',
            'icon' => 'users',
            'href' => route('collection.browse', 'people'),
            'rows' => $collectionRows('people', 'users', $people ?? []),
        ],
        [
            'title' => 'Recent in Places',
            'icon' => 'map-pin',
            'href' => route('collection.browse', 'places'),
            'rows' => $collectionRows('places', 'map-pin', $places ?? []),
        ],
        [
            'title' => 'Recent in Culture',
            'icon' => 'palette',
            'href' => route('collection.browse', 'culture'),
            'rows' => $collectionRows('culture', 'palette', $culture ?? []),
        ],
    ];
@endphp

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 border-t border-[#F0EBE0]/60">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 lg:gap-12">
        @foreach($columns as $column)
            <div class="flex flex-col">
                <div class="flex items-center justify-between gap-3 mb-3 pb-3 border-b border-[#F0EBE0] select-none">
                    <h2 class="flex items-center gap-2 font-display font-black text-sm sm:text-base text-[#0A2240] tracking-tight">
                        <span class="text-[#F6B828]">
                            <x-pp-icon :name="$column['icon']" :size="18" />
                        </span>
                        {{ $column['title'] }}
                    </h2>

                    <a href="{{ $column['href'] }}"
                       class="flex items-center gap-0.5 text-[11px] font-bold uppercase tracking-wide text-[#F6B828] hover:text-[#DAA520] transition-colors whitespace-nowrap">
                        View all
                        <x-pp-icon name="chevron-right" :size="14" />
                    </a>
                </div>

                @forelse($column['rows'] as $row)
                    <a href="{{ $row['href'] }}"
                       class="group flex items-center gap-3.5 py-3.5 border-b border-[#F0EBE0]/60 last:border-0">
                        <span class="h-14 w-14 flex-shrink-0 rounded-xl overflow-hidden border border-[#F0EBE0] flex items-center justify-center text-white"
                              style="background: {{ $row['gradient'] }}">
                            <x-pp-icon :name="$row['icon']" :size="22" />
                        </span>

                        <span class="min-w-0">
                            <span class="block font-display font-bold text-sm text-[#0A2240] leading-snug line-clamp-2 group-hover:text-[#F6B828] transition-colors">
                                {{ $row['title'] }}
                            </span>
                            @if($row['meta'])
                                <span class="block mt-0.5 text-[10px] font-bold uppercase tracking-wide text-[#8A9EB4] truncate">
                                    {{ $row['meta'] }}
                                </span>
                            @endif
                        </span>
                    </a>
                @empty
                    <p class="py-3.5 text-xs font-semibold text-[#8A9EB4]">Nothing here yet.</p>
                @endforelse
            </div>
        @endforeach
    </div>
</section>
