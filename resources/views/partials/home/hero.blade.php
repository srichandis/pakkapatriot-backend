@php
    $heroImage = asset('images/hero.png');
    // Portrait crop that keeps the kids in frame on narrow screens,
    // where the wide banner would crop them away.
    $heroMobileImage = asset('images/hero-mobile.jpg');
@endphp

<section class="relative isolate overflow-hidden min-h-[560px] sm:min-h-[640px] lg:min-h-[720px] flex items-center bg-[#0A2240]">
    {{-- Full-bleed banner image covering the whole hero --}}
    {{-- Mobile gets a portrait crop so the kids stay visible; the wide banner
         would otherwise crop down to its middle and leave them out. --}}
    <img src="{{ $heroMobileImage }}" alt=""
         class="absolute inset-0 -z-20 w-full h-full object-cover object-[38%_center] select-none sm:hidden"
         aria-hidden="true" referrerpolicy="no-referrer">
    <img src="{{ $heroImage }}" alt=""
         class="absolute inset-0 -z-20 w-full h-full object-cover object-center select-none hidden sm:block"
         aria-hidden="true" referrerpolicy="no-referrer">

    {{-- Scrim so the copy stays legible over the banner --}}
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#0A2240]/55 via-[#0A2240]/30 to-transparent pointer-events-none"></div>

    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 lg:py-28">
        <div class="max-w-2xl flex flex-col items-start text-left">
            <div class="space-y-6">
                <div>
                    <p class="font-sans text-sm sm:text-base font-black tracking-[0.35em] text-[#F6B828] uppercase mb-4 select-none">
                        Everywhere
                    </p>
                    <h1 class="font-brush text-5xl sm:text-7xl lg:text-8xl leading-none tracking-wide text-white drop-shadow-lg select-none">
                        KNOW BHĀRAT. <br>
                        <span class="text-[#F6B828]">BE BHĀRAT.</span>
                    </h1>
                </div>

                <p class="font-sans text-lg sm:text-xl text-white/90 font-medium leading-relaxed max-w-lg">
                    Pakka Patriot is your buddy on a journey to explore the real Bhārat – its stories, traditions, people and so much more!
                </p>

                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="#latest-stories" data-scroll-to="latest-stories"
                       class="bg-[#F6B828] hover:bg-[#DAA520] text-white px-8 py-4 rounded-xl text-md font-bold shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 group">
                        EXPLORE STORIES
                        <x-pp-icon name="arrow-right" :size="18" class="transition-transform group-hover:translate-x-1" />
                    </a>
                    <button type="button" data-video-open
                            class="border-2 border-white/80 hover:bg-white/10 text-white px-8 py-4 rounded-xl text-md font-bold transition-all duration-200 flex items-center justify-center gap-2 group">
                        <x-pp-icon name="play" :size="18" fill="#FFFFFF" class="transition-transform group-hover:scale-110" />
                        WATCH VIDEO
                    </button>
                </div>

                {{-- "Curious minds" callout --}}
                <div class="relative pt-6 flex items-start gap-4 select-none ml-2">
                    <svg class="w-16 h-16 text-white animate-bounce-horizontal" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true">
                        <path d="M20,20 C40,40 60,30 80,70" stroke-dasharray="4 4" />
                        <path d="M70,68 L80,70 L78,60" />
                    </svg>
                    <div class="pt-2">
                        <p class="font-brush text-2xl text-white rotate-[-4deg] tracking-wide">
                            Curious minds <br>
                            <span class="text-[#F6B828]">change the country!</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Live badge --}}
    <div class="absolute bottom-6 right-4 sm:right-8 bg-white/95 border border-[#E4DCB9] px-5 py-3 rounded-2xl shadow-lg flex items-center gap-3 select-none">
        <span class="flex h-3 w-3 relative">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
        </span>
        <p class="text-xs font-bold text-[#0A2240] font-sans uppercase tracking-wider">
            Live from PakkaPatriot.com
        </p>
    </div>
</section>
