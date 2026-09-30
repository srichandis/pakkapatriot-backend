@props([
    'product',
])

@php
    /**
     * A merchandise card. Clicking it dispatches `open-product`, which the
     * Livewire product modal (app/Livewire/ProductModal.php) listens for and
     * re-reads from the catalogue — so the page never ships a serialised copy
     * of every product just to open one modal.
     *
     * Shared by the home-page store carousel and the /shop pages so the
     * listings can't drift apart.
     */
@endphp

<div wire:click="$dispatch('open-product', { id: {{ $product['id'] }} })"
     {{ $attributes->merge(['class' => 'group bg-white rounded-3xl overflow-hidden border border-[#F0EBE0] hover:border-[#F6B828]/40 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col cursor-pointer relative h-full']) }}>
    @if($product['on_sale'] ?? false)
        <div class="absolute top-4 left-4 z-10 bg-[#F6B828] text-white text-[10px] font-black px-3 py-1.5 rounded-full flex items-center gap-1 shadow-md select-none">
            <x-pp-icon name="tag" :size="10" fill="white" />
            SALE
        </div>
    @endif

    <div class="relative h-52 sm:h-64 overflow-hidden bg-[#FAF6EC]">
        <img src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}"
             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
             loading="lazy" referrerpolicy="no-referrer">
    </div>

    <div class="p-5 flex-grow flex flex-col items-start text-left">
        <span class="text-[10px] font-black tracking-widest text-[#587760] uppercase mb-1 font-sans">
            {{ $product['category'] ?? 'General' }}
        </span>
        <h3 class="font-display font-bold text-md text-[#0A2240] tracking-tight leading-snug mb-3 group-hover:text-[#F6B828] transition-colors line-clamp-2">
            {{ $product['name'] }}
        </h3>
        <div class="w-full mt-auto pt-3 border-t border-[#F0EBE0]/60 flex items-center justify-between">
            <div class="flex items-baseline gap-1.5 font-sans">
                <span class="font-display font-extrabold text-lg text-[#0A2240]">₹{{ $product['price'] }}</span>
                @if($product['on_sale'] ?? false)
                    <span class="text-xs text-[#8A9EB4] line-through font-semibold">₹{{ $product['regular_price'] ?? $product['price'] }}</span>
                @endif
            </div>
            <span class="w-9 h-9 bg-[#FCFAF5] border border-[#E4DCB9] group-hover:bg-[#F6B828] group-hover:border-[#F6B828] text-[#0A2240] group-hover:text-white rounded-full flex items-center justify-center transition-colors">
                <x-pp-icon name="arrow-up-right" :size="16" />
            </span>
        </div>
    </div>
</div>
