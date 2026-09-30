@php
    $maxCarouselProducts = 8;
    $slidesPerView = 4;
    $carouselProducts = array_slice($products ?? [], 0, $maxCarouselProducts);
    $totalProducts = count($products ?? []);
    $totalPages = max(1, (int) ceil(count($carouselProducts) / $slidesPerView));
@endphp

<section id="shop-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 border-t border-[#F0EBE0]/60 scroll-mt-24"
         @if(count($carouselProducts) > 0) data-shop-carousel data-total-pages="{{ $totalPages }}" @endif>

    {{-- Section header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-4">
        <div class="text-left select-none">
            <span class="text-xs font-black tracking-widest text-[#F6B828] uppercase font-sans flex items-center gap-1.5">
                <x-pp-icon name="shopping-bag" :size="14" class="text-green-600" />
                OFFICIAL MERCHANDISE
            </span>
            <h2 class="font-brush text-4xl sm:text-5xl text-[#0A2240] tracking-wide mt-1">
                PAKKA PATRIOT <span class="text-[#F6B828]">STORE</span>
            </h2>
            @if(count($carouselProducts) < $totalProducts)
                <p class="text-xs font-semibold text-[#8A9EB4] mt-1">
                    Showing {{ count($carouselProducts) }} of {{ $totalProducts }} products
                </p>
            @endif
        </div>

        <div class="flex items-center gap-3">
            @if($totalProducts > $slidesPerView)
                <a href="{{ url('/shop') }}"
                   class="bg-[#0A2240] hover:bg-[#1A3A5E] text-white px-6 py-3 rounded-xl text-sm font-bold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2 group">
                    <x-pp-icon name="shopping-bag" :size="16" />
                    VIEW ALL ({{ $totalProducts }})
                    <x-pp-icon name="arrow-right" :size="16" class="transition-transform group-hover:translate-x-1" />
                </a>
            @endif

            @if($totalPages > 1)
                <div class="flex gap-2">
                    <button type="button" data-shop-prev aria-label="Previous"
                            class="w-11 h-11 rounded-full bg-white border border-[#E4DCB9] hover:bg-[#F6B828] hover:border-[#F6B828] hover:text-white text-[#0A2240] flex items-center justify-center transition-all duration-200 shadow-sm">
                        <x-pp-icon name="chevron-left" :size="20" />
                    </button>
                    <button type="button" data-shop-next aria-label="Next"
                            class="w-11 h-11 rounded-full bg-white border border-[#E4DCB9] hover:bg-[#F6B828] hover:border-[#F6B828] hover:text-white text-[#0A2240] flex items-center justify-center transition-all duration-200 shadow-sm">
                        <x-pp-icon name="chevron-right" :size="20" />
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if(count($carouselProducts) === 0)
        {{-- Empty state --}}
        <div class="bg-[#FAF6EC] border border-[#E4DCB9] rounded-2xl p-12 text-center max-w-md mx-auto">
            <x-pp-icon name="shopping-bag" :size="40" class="mx-auto mb-3 text-[#E4DCB9]" />
            <p class="font-display font-bold text-lg text-[#0A2240] mb-2">No Products Yet</p>
            <p class="text-sm text-[#2F445A] mb-4">Our store is being stocked. Check back soon for Made in Bhārat merchandise.</p>
            <a href="{{ url('/shop') }}" class="inline-block bg-[#F6B828] hover:bg-[#DAA520] text-white px-5 py-2.5 rounded-full text-xs font-bold transition-all shadow">
                Visit the Store
            </a>
        </div>
    @else
        <div class="relative overflow-hidden rounded-3xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($carouselProducts as $product)
                    <div data-shop-slide="{{ $loop->index }}" class="{{ $loop->index >= $slidesPerView ? 'hidden' : '' }}">
                        <x-pp-product-card :product="$product" />
                    </div>
                @endforeach
            </div>

            @if($totalPages > 1)
                <div class="flex items-center justify-center gap-2 mt-8" data-shop-dots>
                    @for($i = 0; $i < $totalPages; $i++)
                        <button type="button" data-shop-dot="{{ $i }}" aria-label="Page {{ $i + 1 }}"
                                class="h-2 rounded-full transition-all duration-300 {{ $i === 0 ? 'w-8 bg-[#F6B828]' : 'w-2 bg-[#E4DCB9] hover:bg-[#F6B828]/50' }}"></button>
                    @endfor
                </div>

                <p class="text-center text-[10px] font-semibold text-[#8A9EB4] mt-4 uppercase tracking-widest select-none">
                    <span data-shop-status>▶ AUTO-SCROLLING</span>
                    <span class="mx-2">·</span>
                    <span data-shop-page>1</span> / {{ $totalPages }}
                    <span class="mx-2">·</span>
                    {{ count($carouselProducts) }} products
                </p>
            @endif

            @if($totalProducts > $slidesPerView)
                <div class="mt-6 text-center md:hidden">
                    <a href="{{ url('/shop') }}"
                       class="inline-flex items-center gap-2 bg-[#0A2240] hover:bg-[#1A3A5E] text-white px-6 py-3 rounded-xl text-sm font-bold transition-all shadow-md">
                        VIEW ALL {{ $totalProducts }} PRODUCTS
                        <x-pp-icon name="arrow-right" :size="16" />
                    </a>
                </div>
            @endif
        </div>
    @endif
</section>
