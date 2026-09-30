@extends('layouts.site')

@php
    /** One storefront category — ported from the React MadeInBharatCategoryPage. */
    $found = $name !== null;
@endphp

@section('title', ($found ? $name.' — Shop' : 'Category not found').' — Pakka Patriot')
@section('description', $found ? ($blurb ?? null) : null)

@section('content')
<div class="min-h-screen bg-brand-cream relative">

    <div class="sticky top-0 z-20 bg-white/90 backdrop-blur-md border-b border-[#F0EBE0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="{{ url('/shop') }}"
               class="flex items-center gap-2 text-sm font-bold text-[#0A2240] hover:text-[#F6B828] transition-colors">
                <x-pp-icon name="arrow-left" :size="18" />
                All products
            </a>
            <span class="text-[10px] font-black tracking-widest text-[#8A9EB4] uppercase">
                {{ $found ? count($products).' products' : 'Category not found' }}
            </span>
        </div>
    </div>

    <div class="absolute top-20 right-0 w-80 h-80 bg-[#F6B828]/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-40 left-0 w-64 h-64 bg-[#F6B828]/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative z-10">
        @if(! $found)
            <div class="text-center py-16 bg-white rounded-3xl border border-[#F0EBE0] max-w-md mx-auto">
                <x-pp-icon name="shopping-bag" :size="64" class="mx-auto text-[#E4DCB9] mb-4" />
                <h3 class="font-display font-bold text-xl text-brand-blue mb-2">Category not found</h3>
                <p class="text-sm text-[#4E637A] font-medium mb-6">That category doesn't exist in our store.</p>
                <a href="{{ url('/shop') }}"
                   class="inline-block px-6 py-3 rounded-full bg-brand-blue text-white text-sm font-bold hover:bg-[#0A2240]/90 transition-colors">
                    Browse all products
                </a>
            </div>
        @else
            {{-- Header --}}
            <div class="text-center mb-10">
                <div class="inline-flex items-center gap-2 bg-brand-blue/5 rounded-full px-4 py-1.5 mb-4">
                    <x-pp-icon name="badge-check" :size="16" class="text-green-600" />
                    <span class="text-xs font-black tracking-widest text-brand-blue uppercase">Proudly of Bhārat</span>
                </div>
                <h1 class="font-brush text-5xl sm:text-6xl lg:text-7xl text-brand-blue tracking-wide leading-tight mb-4">
                    {{ $name }}
                </h1>
                <p class="text-lg sm:text-xl text-[#4E637A] font-medium max-w-2xl mx-auto">
                    {{ $blurb ?? 'Every design, printed on '.mb_strtolower($name).'.' }}
                </p>
            </div>

            {{-- Category nav --}}
            <div class="flex flex-wrap justify-center gap-3 mb-10">
                <a href="{{ url('/shop') }}"
                   class="px-5 py-2 rounded-full text-xs font-bold tracking-wide transition-all duration-200 bg-white border border-[#E4DCB9] text-[#4E637A] hover:border-brand-blue hover:text-brand-blue">
                    ALL
                </a>
                @foreach($categories as $category)
                    <a href="{{ url('/shop/'.App\Http\Controllers\ShopController::categorySlug($category)) }}"
                       class="px-5 py-2 rounded-full text-xs font-bold tracking-wide transition-all duration-200
                              {{ $category === $name ? 'bg-brand-blue text-white shadow-md' : 'bg-white border border-[#E4DCB9] text-[#4E637A] hover:border-brand-blue hover:text-brand-blue' }}">
                        {{ $category }}
                    </a>
                @endforeach
            </div>

            @if(empty($products))
                <div class="text-center py-16 bg-white rounded-3xl border border-[#F0EBE0] max-w-md mx-auto">
                    <x-pp-icon name="shopping-bag" :size="64" class="mx-auto text-[#E4DCB9] mb-4" />
                    <h3 class="font-display font-bold text-xl text-brand-blue mb-2">No Products Yet</h3>
                    <p class="text-sm text-[#4E637A] font-medium">
                        We're still adding products to this category. Check back soon!
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <x-pp-product-card :product="$product" />
                    @endforeach
                </div>
            @endif

            {{-- Other categories --}}
            @php $others = array_values(array_filter($categories, fn ($c) => $c !== $name)); @endphp
            @if($others)
                <div class="mt-16 pt-10 border-t border-[#F0EBE0]">
                    <div class="flex items-center justify-center gap-2 mb-6">
                        <x-pp-icon name="check" :size="20" class="text-[#587760]" />
                        <h2 class="font-display font-bold text-2xl text-brand-blue">Explore other categories</h2>
                    </div>
                    <div class="flex flex-wrap justify-center gap-3">
                        @foreach($others as $category)
                            <a href="{{ url('/shop/'.App\Http\Controllers\ShopController::categorySlug($category)) }}"
                               class="px-5 py-2 rounded-full text-xs font-bold tracking-wide transition-all duration-200 bg-white border border-[#E4DCB9] text-[#4E637A] hover:border-brand-blue hover:text-brand-blue">
                                {{ $category }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
