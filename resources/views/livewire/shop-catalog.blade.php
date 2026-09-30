<div wire:key="shop-catalog">
    {{-- Search --}}
    <form wire:submit="clearSearch" class="relative max-w-md mx-auto mb-6" autocomplete="off">
        <x-pp-icon name="search" :size="20" class="absolute left-4 top-1/2 -translate-y-1/2 text-[#8A9EB4]" />
        <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search products..."
               aria-label="Search products"
               class="w-full bg-white border border-[#E4DCB9] rounded-full pl-12 pr-10 py-3 text-sm font-semibold text-brand-blue focus:outline-none focus:border-[#F6B828] focus:ring-2 focus:ring-[#F6B828]/10 transition-all">
        @if($search !== '')
            <button type="button" wire:click="clearSearch"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8A9EB4] hover:text-[#F6B828] transition-colors"
                    aria-label="Clear search">
                <x-pp-icon name="x" :size="18" />
            </button>
        @endif
    </form>

    {{-- Category chips --}}
    <div class="flex flex-wrap justify-center gap-3">
        <a href="{{ $search === '' ? url('/shop') : url('/shop').'?q='.rawurlencode($search) }}"
           class="px-5 py-2 rounded-full text-xs font-bold tracking-wide transition-all duration-200
                  {{ $search === '' ? 'bg-brand-blue text-white shadow-md' : 'bg-white border border-[#E4DCB9] text-[#4E637A] hover:border-brand-blue hover:text-brand-blue' }}">
            ALL
        </a>
        @foreach($categories as $category)
            <a href="{{ url('/shop/'.\App\Http\Controllers\ShopController::categorySlug($category)) }}"
               class="px-5 py-2 rounded-full text-xs font-bold tracking-wide transition-all duration-200 bg-white border border-[#E4DCB9] text-[#4E637A] hover:border-brand-blue hover:text-brand-blue">
                {{ $category }}
            </a>
        @endforeach
    </div>

    {{-- Grid --}}
    <div class="mt-10">
        @if(empty($products))
            <div class="text-center py-16 bg-white rounded-3xl border border-[#F0EBE0] max-w-md mx-auto">
                <x-pp-icon name="shopping-bag" :size="64" class="mx-auto text-[#E4DCB9] mb-4" />
                <h3 class="font-display font-bold text-xl text-brand-blue mb-2">No Products Found</h3>
                <p class="text-sm text-[#4E637A] font-medium">
                    {{ $search !== '' ? 'Try a different search term.' : 'Try a different category filter.' }}
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <x-pp-product-card :product="$product" wire:key="shop-product-{{ $product['id'] }}" />
                @endforeach
            </div>
        @endif
    </div>
</div>
