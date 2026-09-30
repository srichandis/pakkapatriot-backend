@php
    $exploreLinks = [
        ['label' => 'Stories', 'href' => route('shop.blog.index')],
        ['label' => 'Explore', 'href' => url('/explore')],
        ['label' => 'Ideas', 'href' => route('collection.browse', 'ideas')],
        ['label' => 'Traditions', 'href' => route('collection.browse', 'culture')],
        ['label' => 'People', 'href' => route('collection.browse', 'people')],
        ['label' => 'Places', 'href' => route('collection.browse', 'places')],
        ['label' => 'Create', 'href' => url('/create')],
        ['label' => 'Fun Zone', 'href' => url('/play')],
    ];

    $socials = [
        ['title' => 'Instagram', 'href' => 'https://www.instagram.com/pakkapatriot/', 'class' => 'bg-gradient-to-tr from-[#FFB800] via-[#FF007A] to-[#9E00FF] hover:opacity-90', 'icon' => 'instagram'],
        ['title' => 'YouTube', 'href' => 'https://www.youtube.com/results?search_query=Pakka+Patriot', 'class' => 'bg-[#FF0000] hover:bg-[#CC0000]', 'icon' => 'youtube'],
        ['title' => 'Facebook', 'href' => 'https://www.facebook.com/search/top?q=Pakka%20Patriot', 'class' => 'bg-[#1877F2] hover:bg-[#165EBF]', 'icon' => 'facebook'],
    ];
@endphp

<footer class="bg-[#0A1A2E] text-[#B5CADF] border-t-4 border-[#F6B828] pt-16 pb-8 relative overflow-hidden select-none">
    {{-- Decorative arches --}}
    <div class="absolute top-0 right-0 w-80 h-80 border-t border-r border-white/5 rounded-tr-full pointer-events-none -translate-y-20 translate-x-20"></div>
    <div class="absolute bottom-0 left-0 w-60 h-60 border-b border-l border-white/5 rounded-bl-full pointer-events-none translate-y-10 -translate-x-10"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 lg:gap-8 items-start mb-12">

            {{-- Brand --}}
            <div class="lg:col-span-4 flex flex-col items-start text-left space-y-5">
                <a href="{{ url('/') }}" class="flex items-center" data-scroll-top>
                    <img src="{{ asset('images/logo-light.png') }}" alt="Pakka Patriot" class="h-14 w-auto object-contain">
                </a>

                <p class="text-sm text-[#8EA6C0] font-medium leading-relaxed max-w-xs">
                    Know Bhārat. Be Bhārat. A space for curious minds to learn, explore, and make a positive impact.
                </p>

                <button type="button" data-open-journey
                        class="bg-[#F6B828] hover:bg-[#DAA520] text-white font-bold text-xs px-6 py-3 rounded-full transition-all duration-200 shadow hover:shadow-lg transform hover:-translate-y-0.5">
                    Join the Journey
                </button>
            </div>

            {{-- Explore --}}
            <div class="lg:col-span-2 flex flex-col items-start text-left space-y-4">
                <h4 class="text-white font-display font-extrabold text-sm tracking-widest uppercase">EXPLORE</h4>
                <ul class="space-y-2 text-sm font-semibold">
                    @foreach($exploreLinks as $link)
                        <li>
                            <a href="{{ $link['href'] }}" class="hover:text-white transition-colors">{{ $link['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- About --}}
            <div class="lg:col-span-2 flex flex-col items-start text-left space-y-4">
                <h4 class="text-white font-display font-extrabold text-sm tracking-widest uppercase">ABOUT</h4>
                <ul class="space-y-2 text-sm font-semibold">
                    <li><a href="{{ url('/about') }}" class="hover:text-white transition-colors">About Us</a></li>
                    <li><a href="{{ url('/about') }}" class="hover:text-white transition-colors">Our Mission</a></li>
                    <li><button type="button" data-open-journey class="hover:text-white transition-colors">Join Us</button></li>
                    <li><a href="mailto:support@pakkapatriot.com" class="hover:text-white transition-colors">Contact Us</a></li>
                </ul>
            </div>

            {{-- Follow --}}
            <div class="lg:col-span-4 flex flex-col items-start lg:items-end space-y-6 text-left lg:text-right">
                <div>
                    <h4 class="text-white font-display font-extrabold text-sm tracking-widest uppercase mb-3">FOLLOW US</h4>
                    <div class="flex gap-3">
                        @foreach($socials as $social)
                            <a href="{{ $social['href'] }}" target="_blank" rel="noopener noreferrer" title="{{ $social['title'] }}"
                               class="w-10 h-10 {{ $social['class'] }} text-white rounded-full flex items-center justify-center transition-all shadow-md">
                                <x-pp-icon :name="$social['icon']" :size="18" />
                            </a>
                        @endforeach

                        {{-- X (Twitter) --}}
                        <a href="https://x.com/search?q=Pakka%20Patriot" target="_blank" rel="noopener noreferrer" title="X (Twitter)"
                           class="w-10 h-10 bg-black hover:bg-neutral-800 text-white rounded-full flex items-center justify-center transition-all shadow-md">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Sticky note --}}
                <div class="bg-[#FAF4E4] border-2 border-[#E4DCB9] text-[#0A2240] p-4 rounded-xl shadow-lg rotate-[3deg] hover:rotate-0 transition-transform duration-300 max-w-xs relative text-left">
                    <div class="absolute top-0 right-4 w-4 h-4 bg-[#D1C7A3] rounded-bl-full pointer-events-none"></div>
                    <p class="font-brush text-xl tracking-wide select-none">
                        Be Informed. <br>
                        Be Inspired. <br>
                        <span class="text-[#F6B828]">Be Bhārat. 🧡</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="border-t border-[#1F3D5E] pt-8 mt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs font-semibold text-[#8EA6C0]">
            <p>© {{ date('Y') }} Pakka Patriot Website. Crafted with love for Bhārat.</p>
            <div class="flex gap-6">
                <a href="{{ url('/privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="{{ url('/terms') }}" class="hover:text-white transition-colors">Terms of Service</a>
                <button type="button" data-scroll-top class="flex items-center gap-1.5 text-[#F6B828] hover:text-white transition-colors font-bold">
                    Back to top
                    <x-pp-icon name="arrow-up" :size="14" />
                </button>
            </div>
        </div>
    </div>
</footer>
