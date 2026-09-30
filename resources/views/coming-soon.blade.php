@extends('layouts.site')

@section('title', $title.' — Coming Soon — Pakka Patriot')
@section('description', $blurb)

@section('content')
    <section id="coming-soon" class="bg-brand-cream relative overflow-hidden">
        {{-- Decorative background --}}
        <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-[#FBECE6]/40 to-transparent pointer-events-none"></div>
        <div class="absolute top-24 right-10 w-48 h-48 bg-[#F6B828]/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-24 left-10 w-64 h-64 bg-brand-blue/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 relative z-10">

            {{-- Hero --}}
            <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-20">
                <div class="flex items-center justify-center gap-3 mb-6 select-none">
                    <div class="w-8 h-px bg-[#F6B828]"></div>
                    <x-pp-icon name="star" :size="20" fill="#F6B828" class="text-[#F6B828]" />
                    <div class="w-8 h-px bg-[#F6B828]"></div>
                </div>

                <div class="w-20 h-20 mx-auto rounded-3xl bg-brand-blue shadow-lg flex items-center justify-center mb-6 rotate-3">
                    <x-pp-icon :name="$icon" :size="38" class="text-[#F6B828]" />
                </div>

                <div class="inline-flex items-center gap-2 bg-white rounded-full px-5 py-2 border border-[#F0EBE0] shadow-sm mb-6">
                    <x-pp-icon name="clock" :size="15" class="text-[#F6B828]" />
                    <span class="font-display font-black text-xs tracking-widest text-brand-blue uppercase">
                        {{ $eyebrow }} &middot; Coming Soon
                    </span>
                </div>

                <h1 class="font-brush text-4xl sm:text-5xl lg:text-6xl text-brand-blue tracking-wide leading-tight mb-4">
                    {{ $heading }} <span class="text-[#F6B828]">{{ $accent }}</span>
                </h1>

                <p class="font-sans text-lg sm:text-xl text-[#4E637A] font-medium leading-relaxed max-w-2xl mx-auto">
                    {{ $blurb }}
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center pt-8">
                    <button type="button" data-open-journey
                            class="bg-[#F6B828] hover:bg-[#DAA520] text-white px-8 py-4 rounded-xl text-md font-bold shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 group">
                        TELL ME WHEN IT'S LIVE
                        <x-pp-icon name="arrow-right" :size="18" class="transition-transform group-hover:translate-x-1" />
                    </button>
                    <a href="{{ url('/') }}"
                       class="border-2 border-brand-blue/20 hover:bg-brand-blue/5 text-brand-blue px-8 py-4 rounded-xl text-md font-bold transition-all duration-200 flex items-center justify-center gap-2">
                        <x-pp-icon name="arrow-left" :size="18" />
                        BACK TO HOME
                    </a>
                </div>
            </div>

            {{-- What's coming --}}
            <div class="mb-16 sm:mb-20">
                <div class="text-center mb-10">
                    <h2 class="font-display font-bold text-2xl sm:text-3xl text-brand-blue">What's being built</h2>
                    <p class="text-[#4E637A] font-semibold mt-2 max-w-xl mx-auto">
                        A first look at what {{ $title }} will hold.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($highlights as $item)
                        <div class="bg-white rounded-3xl border border-[#F0EBE0] p-6 shadow-md hover:border-[#F6B828]/30 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                            <div class="w-14 h-14 rounded-2xl bg-[#FBECE6] flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-200">
                                <x-pp-icon :name="$item['icon']" :size="28" class="text-[#F6B828]" />
                            </div>
                            <h3 class="font-display font-black text-base text-brand-blue mb-2">{{ $item['title'] }}</h3>
                            <p class="text-sm text-[#4E637A] font-medium leading-relaxed">{{ $item['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Available now --}}
            <div class="bg-white rounded-3xl border border-[#F0EBE0] shadow-xl p-8 sm:p-10 text-center">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <x-pp-icon name="sparkles" :size="18" class="text-[#F6B828]" />
                    <span class="text-xs font-black tracking-widest text-brand-sage uppercase">Available right now</span>
                </div>

                <p class="text-[#4E637A] font-semibold max-w-lg mx-auto mb-6">
                    In the meantime, there's plenty to read, print and play.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @foreach($alsoLive as $link)
                        <a href="{{ $link['href'] }}"
                           class="bg-brand-cream hover:bg-[#FEF5E0] border border-[#F0EBE0] hover:border-[#F6B828]/40 text-brand-blue px-6 py-3 rounded-full text-sm font-bold transition-all duration-200 inline-flex items-center justify-center gap-2">
                            {{ $link['label'] }}
                            <x-pp-icon name="arrow-right" :size="15" class="text-[#F6B828]" />
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
