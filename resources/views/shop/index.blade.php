@extends('layouts.site')

@section('title', 'Shop — Made in Bhārat — Pakka Patriot')
@section('description', 'Discover products crafted, designed, and made in Bhārat — from handloom traditions to modern merchandise.')

@php
    /**
     * The store — ported from the React MadeInIndiaPage: brush-script header,
     * Livewire product search and category chips over a 4-up product grid.
     */
@endphp

@section('content')
<div class="min-h-screen bg-brand-cream relative">

    {{-- Sticky top bar --}}
    <div class="sticky top-0 z-20 bg-white/90 backdrop-blur-md border-b border-[#F0EBE0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="{{ url('/') }}"
               class="flex items-center gap-2 text-sm font-bold text-[#0A2240] hover:text-[#F6B828] transition-colors">
                <x-pp-icon name="arrow-left" :size="18" />
                Back
            </a>
            <span class="text-[10px] font-black tracking-widest text-[#8A9EB4] uppercase">
                {{ count($products) }} products
            </span>
        </div>
    </div>

    <div class="absolute top-20 right-0 w-80 h-80 bg-[#F6B828]/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-40 left-0 w-64 h-64 bg-[#F6B828]/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative z-10">

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 bg-brand-blue/5 rounded-full px-4 py-1.5 mb-4">
                <x-pp-icon name="badge-check" :size="16" class="text-green-600" />
                <span class="text-xs font-black tracking-widest text-brand-blue uppercase">Proudly of Bhārat</span>
            </div>
            <h1 class="font-brush text-5xl sm:text-6xl lg:text-7xl text-brand-blue tracking-wide leading-tight mb-4">
                Made in <span class="text-[#F6B828]">Bhārat</span>
            </h1>
            <p class="text-lg sm:text-xl text-[#4E637A] font-medium max-w-2xl mx-auto">
                Discover products crafted, designed, and made in Bhārat — from handloom traditions to modern merchandise.
            </p>
        </div>

        {{-- Search, category filters and the product grid (Livewire) --}}
        <div class="mb-10">
            <livewire:shop-catalog />
        </div>
    </div>
</div>
@endsection
