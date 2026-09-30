@php
    $hasQuery = trim($query) !== '';
@endphp

<form wire:submit="submit" class="relative flex items-center" autocomplete="off" wire:key="site-search-box">
    <input type="text"
           wire:model.live.debounce.300ms="query"
           wire:keydown.arrow-down.prevent="moveHighlight(1)"
           wire:keydown.arrow-up.prevent="moveHighlight(-1)"
           wire:keydown.enter.prevent="handleEnter"
           wire:keydown.escape="clearHighlight"
           placeholder="Search the whole site…"
           aria-label="Search the whole site"
           aria-expanded="{{ $hasQuery ? 'true' : 'false' }}"
           class="transition-all duration-300 ease-in-out text-sm text-[#0A2240] bg-[#FAF6EC] border border-[#E4DCB9] rounded-full focus:outline-none focus:border-[#F6B828] focus:ring-1 focus:ring-[#F6B828]
                  {{ $expanded ? 'w-44 sm:w-60 px-4 py-1.5 opacity-100' : 'w-0 px-0 py-0 opacity-0 pointer-events-none' }}">

    <button type="button" wire:click="toggle"
            class="p-2 text-[#0A2240] hover:text-[#F6B828] transition-colors rounded-full hover:bg-[#FAF6EC]"
            title="Search">
        @if($expanded && $hasQuery)
            <x-pp-icon name="x" :size="20" />
        @elseif($expanded)
            <x-pp-icon name="x" :size="20" />
        @else
            <img src="{{ asset('images/nav/search.png') }}" alt="" aria-hidden="true" class="h-6 w-6 object-contain">
        @endif
    </button>

    {{-- Live suggestions --}}
    @if($hasQuery)
        <div class="absolute right-0 top-full mt-2 z-50 w-[min(340px,calc(100vw-4rem))] bg-white rounded-2xl border border-[#E4DCB9] shadow-2xl overflow-hidden text-left">
            <div class="max-h-80 overflow-y-auto py-1.5">
                @if(empty($suggestions))
                    <div class="px-4 py-6 text-center">
                        <p class="text-sm font-bold text-[#0A2240]">No suggestions</p>
                        <p class="text-xs text-[#8A9EB4] font-semibold mt-0.5">Press Enter to search the whole site.</p>
                    </div>
                @else
                    @foreach($suggestions as $index => $suggestion)
                        <button type="button" wire:key="suggestion-{{ $index }}"
                                wire:click="openSuggestion({{ $index }})"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-left transition-colors cursor-pointer
                                       {{ $highlighted === $index ? 'bg-[#FAF6EC]' : 'bg-white' }}">
                            <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#0A2240] to-[#1F3D5E] flex items-center justify-center flex-shrink-0">
                                <x-pp-icon :name="$suggestion['kind_icon']" :size="15" class="text-white" />
                            </span>
                            <span class="flex-grow min-w-0">
                                <span class="block text-sm font-bold text-[#0A2240] truncate">{{ $suggestion['title'] }}</span>
                                <span class="block text-[11px] text-[#8A9EB4] font-semibold truncate">{{ $suggestion['subtitle'] }}</span>
                            </span>
                            <span class="text-[9px] font-black uppercase tracking-wider text-[#587760] bg-[#EAF1EB] px-2 py-0.5 rounded-full flex-shrink-0">
                                {{ $suggestion['kind_label'] }}
                            </span>
                        </button>
                    @endforeach
                @endif
            </div>

            <a href="{{ url('/search').'?q='.rawurlencode(trim($query)) }}"
               class="flex w-full items-center justify-center gap-1.5 border-t border-[#F0EBE0] bg-[#FAF6EC] hover:bg-[#FEF5E0] px-4 py-2.5 text-xs font-black text-[#0A2240] uppercase tracking-wider transition-colors">
                See all results
                <x-pp-icon name="arrow-right" :size="13" />
            </a>
        </div>
    @endif
</form>
