@props(['suggestion'])

{{-- One row of the header's live search dropdown, injected as HTML by resources/js/app.js. --}}
<button type="button" data-suggestion-url="{{ $suggestion['url'] }}"
        class="w-full flex items-center gap-3 px-4 py-2.5 text-left transition-colors cursor-pointer bg-white hover:bg-[#FAF6EC]">
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
