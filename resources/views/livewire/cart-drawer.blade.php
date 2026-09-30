<div wire:key="cart-overlays">
{{-- Floating cart badge --}}
@if($totalItems > 0)
    <div class="fixed bottom-6 right-6 z-30">
        <button type="button" wire:click="openDrawer"
                class="bg-[#F6B828] text-white p-4 rounded-full shadow-2xl hover:bg-[#DAA520] transition-all duration-200 transform hover:scale-110 flex items-center gap-2 select-none font-bold"
                aria-label="Open your bag">
            <x-pp-icon name="shopping-cart" :size="22" class="animate-bounce" />
            <span class="bg-white text-[#F6B828] px-2.5 py-0.5 rounded-full text-xs min-w-[24px]">{{ $totalItems }}</span>
        </button>
    </div>
@endif

{{-- Bag drawer --}}
@if($open)
    <div class="fixed inset-0 z-50 overflow-hidden bg-black/60 backdrop-blur-sm flex justify-end"
         wire:key="cart-drawer-overlay">
        <div class="bg-brand-cream max-w-md w-full h-full shadow-2xl flex flex-col border-l border-[#F0EBE0] text-left">

            {{-- Drawer header --}}
            <div class="p-6 border-b border-[#F0EBE0] flex justify-between items-center bg-white">
                <div class="flex items-center gap-2">
                    <x-pp-icon name="shopping-cart" :size="20" class="text-[#F6B828]" />
                    <h3 class="font-display font-black text-lg text-[#0A2240]">YOUR BAG ({{ $totalItems }})</h3>
                </div>
                <button type="button" wire:click="close"
                        class="text-[#0A2240] p-1.5 hover:bg-gray-100 rounded-full cursor-pointer"
                        title="Close Drawer">
                    <x-pp-icon name="x" :size="20" />
                </button>
            </div>

            {{-- Items --}}
            <div class="flex-grow overflow-y-auto p-6 space-y-3">
                @forelse($items as $item)
                    <div class="flex gap-4 p-4 bg-white rounded-2xl border border-[#F0EBE0] items-center" wire:key="cart-line-{{ $item['product']['id'] }}">
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-[#FAF6EC] flex-shrink-0">
                            <img src="{{ $item['product']['image_url'] }}" alt="{{ $item['product']['name'] }}"
                                 class="w-full h-full object-cover" referrerpolicy="no-referrer">
                        </div>
                        <div class="flex-grow min-w-0 space-y-0.5">
                            <span class="text-[9px] font-black text-[#587760] uppercase tracking-wide block">
                                {{ $item['product']['category'] }}
                            </span>
                            <h4 class="font-display font-bold text-xs text-[#0A2240] truncate">{{ $item['product']['name'] }}</h4>
                            <p class="font-sans font-black text-sm text-[#F6B828]">₹{{ $item['product']['price'] }}</p>
                        </div>

                        <div class="flex items-center border border-[#DCD3B5] rounded-lg overflow-hidden flex-shrink-0">
                            <button type="button" wire:click="decrement({{ $item['product']['id'] }})"
                                    class="px-2 py-1 hover:bg-[#FAF6EC] text-[#0A2240] transition-colors cursor-pointer"
                                    aria-label="Decrease quantity">
                                <x-pp-icon name="minus" :size="12" />
                            </button>
                            <span class="px-2 py-1 text-xs font-bold text-[#0A2240] min-w-[22px] text-center border-x border-[#DCD3B5] select-none">
                                {{ $item['quantity'] }}
                            </span>
                            <button type="button" wire:click="increment({{ $item['product']['id'] }})"
                                    class="px-2 py-1 hover:bg-[#FAF6EC] text-[#0A2240] transition-colors cursor-pointer"
                                    aria-label="Increase quantity">
                                <x-pp-icon name="plus" :size="12" />
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-[#8A9EB4] font-semibold text-sm">
                        <x-pp-icon name="shopping-bag" :size="40" class="mx-auto mb-3 opacity-30" />
                        Your bag is empty
                    </div>
                @endforelse
            </div>

            {{-- Footer --}}
            <div class="p-6 border-t border-[#F0EBE0] bg-white space-y-3">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-sm text-[#0A2240]">Subtotal</span>
                    <span class="text-xl font-black text-[#F6B828]">₹{{ number_format($totalPrice) }}</span>
                </div>

                <div class="space-y-2">
                    @if($totalItems > 0)
                        <a href="{{ url('/checkout') }}"
                           class="w-full bg-[#F6B828] hover:bg-[#DAA520] text-white py-3.5 rounded-xl font-bold text-sm shadow hover:shadow-lg transition-all flex items-center justify-center gap-2 select-none">
                            <x-pp-icon name="credit-card" :size="16" />
                            PROCEED TO CHECKOUT
                            <x-pp-icon name="arrow-right" :size="16" />
                        </a>
                    @endif

                    <button type="button" wire:click="clear"
                            class="w-full text-center text-xs text-gray-400 hover:text-red-500 font-semibold py-1 cursor-pointer">
                        Empty Bag
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
</div>
