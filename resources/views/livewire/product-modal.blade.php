<div wire:key="product-modal-root">
@if($product !== null)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
         wire:click.self="close" wire:key="product-modal">
        <div class="relative bg-brand-cream max-w-3xl w-full rounded-3xl overflow-hidden shadow-2xl border border-[#F0EBE0] max-h-[90vh] flex flex-col">

            <button type="button" wire:click="close"
                    class="absolute top-4 right-4 z-20 bg-black/60 hover:bg-black/80 text-white p-2.5 rounded-full transition-all duration-200"
                    title="Close Modal">
                <x-pp-icon name="x" :size="18" />
            </button>

            <div class="overflow-y-auto flex-grow">
                <div class="flex flex-col md:flex-row">

                    {{-- Image column --}}
                    <div class="w-full md:w-1/2 h-64 sm:h-80 md:h-[450px] relative overflow-hidden bg-[#FAF6EC]">
                        <img src="{{ $active }}" alt="{{ $product['name'] }}"
                             class="w-full h-full object-cover" referrerpolicy="no-referrer">

                        @if($product['on_sale'] ?? false)
                            <div class="absolute top-4 left-4 bg-[#F6B828] text-white text-xs font-black px-4 py-1.5 rounded-full shadow-md select-none">
                                SALE ACTIVE
                            </div>
                        @endif

                        @if(count($images) > 1)
                            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2 bg-white/90 backdrop-blur rounded-full px-4 py-2 shadow-lg border border-[#F0EBE0]">
                                @foreach($images as $index => $image)
                                    @php $label = $this->colourLabel($image); @endphp
                                    <button type="button" wire:click="selectImage({{ $index }})"
                                            title="{{ $label }}" aria-label="{{ $label }} colour"
                                            style="background-color: {{ $this->colourHex($label) }}"
                                            class="w-7 h-7 rounded-full border-2 transition-all duration-200 cursor-pointer
                                                   {{ $index === $imageIndex ? 'border-[#F6B828] scale-110 ring-2 ring-[#F6B828]/30' : 'border-[#E4DCB9] hover:border-[#8A9EB4]' }}"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Info column --}}
                    <div class="w-full md:w-1/2 p-6 sm:p-8 flex flex-col text-left space-y-5 justify-between">
                        <div>
                            <span class="text-[10px] font-black tracking-widest text-[#587760] uppercase mb-1 block">
                                {{ $product['category'] }}
                            </span>

                            <h2 class="font-display font-black text-xl sm:text-2xl text-[#0A2240] tracking-tight leading-tight">
                                {{ $product['name'] }}
                            </h2>

                            @if(count($images) > 1)
                                <p class="text-[11px] font-black tracking-widest text-[#587760] uppercase mt-1 font-sans">
                                    Colour: <span class="text-[#F6B828]">{{ $this->colourLabel($active) }}</span>
                                </p>
                            @endif

                            <div class="flex items-baseline gap-2 mt-3 font-sans border-b border-[#F0EBE0] pb-4">
                                <span class="font-display font-black text-2xl text-[#F6B828]">₹{{ $product['price'] }}</span>
                                @if($product['on_sale'] ?? false)
                                    <span class="text-sm text-[#8A9EB4] line-through font-bold">₹{{ $product['regular_price'] }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-3 flex-grow py-3">
                            <h4 class="text-xs font-black tracking-wider text-[#0A2240] uppercase">Description</h4>
                            <p class="text-xs sm:text-sm text-[#4E637A] font-medium leading-relaxed font-sans">
                                {{ $product['description'] ?: ($product['short_description'] ?: 'Premium quality handcrafted merchandise designed to instill a proud patriot vibe. Perfect for daily wear, gifting, or study spaces.') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3 pt-2 pb-1">
                            <span class="text-xs font-black text-[#0A2240] uppercase tracking-wider">Qty:</span>
                            <div class="flex items-center border border-[#DCD3B5] rounded-xl overflow-hidden bg-white">
                                <button type="button" wire:click="decrement"
                                        class="px-3 py-2 text-sm font-bold hover:bg-[#FAF6EC] text-[#0A2240] transition-colors">
                                    <x-pp-icon name="minus" :size="14" />
                                </button>
                                <span class="px-4 py-2 text-sm font-black text-[#0A2240] min-w-[32px] text-center border-x border-[#DCD3B5] select-none">
                                    {{ $quantity }}
                                </span>
                                <button type="button" wire:click="increment"
                                        class="px-3 py-2 text-sm font-bold hover:bg-[#FAF6EC] text-[#0A2240] transition-colors">
                                    <x-pp-icon name="plus" :size="14" />
                                </button>
                            </div>
                        </div>

                        <div class="space-y-3 pt-4 border-t border-[#F0EBE0]">
                            <div class="flex gap-2 w-full">
                                <button type="button" wire:click="addToCart(false)"
                                        class="flex-grow py-3.5 rounded-xl font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-1.5 select-none border
                                               {{ $justAdded
                                                    ? 'bg-green-500 border-green-500 text-white shadow-md'
                                                    : 'bg-white hover:bg-[#FAF6EC] border-[#DCD3B5] text-[#0A2240]' }}">
                                    @if($justAdded)
                                        <span>ADDED ×{{ $quantity }}!</span>
                                    @else
                                        <x-pp-icon name="shopping-bag" :size="16" />
                                        ADD TO BAG — ₹{{ (int) preg_replace('/[^0-9]/', '', (string) $product['price']) * $quantity }}
                                    @endif
                                </button>

                                <button type="button" wire:click="addToCart(true)"
                                        class="bg-[#F6B828] hover:bg-[#DAA520] text-white px-5 py-3.5 rounded-xl font-bold text-xs sm:text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-1.5 select-none flex-shrink-0">
                                    BUY NOW
                                    <x-pp-icon name="arrow-right" :size="16" />
                                </button>
                            </div>

                            <span class="text-[10px] text-center block text-[#8A9EB4] font-bold uppercase tracking-wider font-sans">
                                Stock status: {{ ($product['in_stock'] ?? true) ? '✅ In Stock' : '❌ Out of Stock' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
</div>
