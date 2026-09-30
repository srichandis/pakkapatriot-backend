@php
    /*
     * "Today's Panchangam" card — the right-hand column of the Latest Stories
     * row. Light card on the hero palette (navy #0A2240 text, gold #F6B828
     * accents, cream #FCFAF5 surface), with a calendar icon in the top-left.
     *
     * The #panchangaRoot markup (and the #panchangaTime / #panchangaDate /
     * #panchangaLocation / #panchangaError nodes) is what resources/js/panchanga.js
     * looks for, so the shared script fills the values in on load. Until it runs
     * the fields show dashes.
     *
     * Unlike the full page this card asks for no geolocation permission: the
     * script only prompts when the root carries data-panchanga-locate="auto".
     */
    $panchangaFields = [
        ['tithi', 'Tithi'],
        ['nakshatra', 'Nakshatra'],
        ['yoga', 'Yoga'],
        ['karana', 'Karana'],
    ];
@endphp

<div id="panchangaRoot" class="h-full">
    <div class="h-full flex flex-col rounded-3xl border border-[#F0EBE0] bg-gradient-to-b from-[#FCFAF5] to-white p-6 shadow-sm">

        <div class="flex items-center justify-between gap-3 mb-5">
            <div class="flex items-center gap-3 min-w-0">
                <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-[#F6B828]/15 border border-[#F6B828]/30 text-[#DAA520]">
                    <x-pp-icon name="calendar" :size="18" />
                </span>
                <h3 class="font-display font-black text-sm sm:text-base text-[#0A2240] tracking-tight truncate">
                    Today's Panchangam
                </h3>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#F6B828]">IST</span>
        </div>

        <div class="mb-5">
            <div id="panchangaDate" class="font-display font-extrabold text-lg sm:text-xl text-[#0A2240] leading-tight">Loading…</div>
            <div class="flex items-center gap-2 mt-1">
                <span id="panchangaTime" class="font-mono text-xs font-bold text-[#DAA520] tabular-nums">--:--:--</span>
                <span class="text-[11px] text-[#8A9EB4]">·</span>
                <span id="panchangaLocation" class="text-[11px] text-[#8A9EB4]">Bangalore (default)</span>
            </div>
        </div>

        <div id="panchangaError" style="display:none;"
             class="mb-4 items-center gap-2 rounded-xl bg-[#FEF2F2] px-3 py-2 text-[11px] font-semibold text-[#B42828]">
            <x-pp-icon name="alert-triangle" :size="14" /> Panchangam unavailable
        </div>

        <div class="flex-grow divide-y divide-[#F0EBE0]">
            @foreach($panchangaFields as [$key, $label])
                <div data-panchanga-field="{{ $key }}" class="flex items-baseline justify-between gap-3 py-3">
                    <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#8A9EB4] flex-shrink-0">{{ $label }}</span>
                    <span class="text-right min-w-0">
                        <span class="block font-display font-bold text-sm text-[#0A2240]" data-panchanga-value>—</span>
                        <span class="block text-[10px] italic text-[#8A9EB4]" data-panchanga-sub></span>
                    </span>
                </div>
            @endforeach
        </div>

        <a href="{{ route('panchangam') }}"
           class="mt-5 flex items-center justify-center gap-2 rounded-2xl bg-[#F6B828] hover:bg-[#DAA520] px-4 py-3 text-[11px] font-black uppercase tracking-wide text-white transition-colors">
            View Full Panchangam
            <x-pp-icon name="arrow-right" :size="14" />
        </a>
    </div>
</div>
